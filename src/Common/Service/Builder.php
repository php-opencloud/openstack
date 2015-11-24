<?php

namespace OpenStack\Common\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Middleware as GuzzleMiddleware;
use OpenStack\Common\Auth\ServiceUrlResolver;
use OpenStack\Common\Auth\Token;
use OpenStack\Common\Transport\HandlerStack;
use OpenStack\Common\Transport\Middleware;
use OpenStack\Common\Transport\Utils;
use OpenStack\Identity\v3\Service;

/**
 * A Builder for easily creating OpenStack services.
 *
 * @package OpenStack\Common\Service
 */
class Builder
{
    /**
     * Global options that will be applied to every service created by this builder.
     *
     * @var array
     */
    private $globalOptions = [];

    /**
     * Defaults that will be applied to options if no values are provided by the user.
     *
     * @var array
     */
    private $defaults = ['urlType' => 'publicURL'];

    /**
     * @param array $globalOptions Options that will be applied to every service created by this builder.
     *                             Eventually they will be merged (and if necessary overridden) by the
     *                             service-specific options passed in.
     */
    public function __construct(array $globalOptions = [])
    {
        $this->globalOptions = $globalOptions;
    }

    /**
     * Internal method which resolves the API and Service classes for a service.
     *
     * @param string $serviceName    The name of the service, e.g. Compute
     * @param int    $serviceVersion The major version of the service, e.g. 2
     *
     * @return array
     */
    private function getClasses($serviceName, $serviceVersion)
    {
        $rootNamespace = sprintf("OpenStack\\%s\\v%d", $serviceName, $serviceVersion);

        return [
            sprintf("%s\\Api", $rootNamespace),
            sprintf("%s\\Service", $rootNamespace),
        ];
    }

    /**
     * This method will return an OpenStack service ready fully built and ready for use. There is
     * some initial setup that may prohibit users from directly instantiating the service class
     * directly - this setup includes the configuration of the HTTP client's base URL, and the
     * attachment of an authentication handler.
     *
     * @param $serviceName          The name of the service as it appears in the OpenStack\* namespace
     * @param $serviceVersion       The major version of the service
     * @param array $serviceOptions The service-specific options to use
     *
     * @return \OpenStack\Common\Service\ServiceInterface
     *
     * @throws \Exception
     */
    public function createService($serviceName, $serviceVersion, array $serviceOptions = [])
    {
        $options = $this->mergeOptions($serviceOptions);

        if (!isset($options['identityService'])) {
            $httpClient = $this->httpClient($options['authUrl'], HandlerStack::create());
            $options['identityService'] = Service::factory($httpClient);
        }

        if (!isset($options['authHandler'])) {
            $options['authHandler'] = function () use ($options) {
                return $options['identityService']->generateToken($options);
            };
        }

        if (!isset($options['httpClient']) || !($options['httpClient'] instanceof ClientInterface)) {
            if (strcasecmp($serviceName, 'identity') === 0) {
                $baseUrl = $options['authUrl'];
                $stack = $this->getStack($options['authHandler']);
            } else {
                list($token, $baseUrl) = $options['identityService']->authenticate($options);
                $stack = $this->getStack($options['authHandler'], $token);
            }

            if (!empty($options['debugLog'])) {
                $stack->push(GuzzleMiddleware::log($options['logger'], $options['messageFormatter']));
            }

            $options['httpClient'] = $this->httpClient($baseUrl, $stack);
        }

        list($apiClass, $serviceClass) = $this->getClasses($serviceName, $serviceVersion);

        return new $serviceClass($options['httpClient'], new $apiClass());
    }

    private function getStack(callable $authHandler, Token $token = null)
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::authHandler($authHandler, $token));
        return $stack;
    }

    private function httpClient($baseUrl, HandlerStack $stack)
    {
        return new Client([
            'base_uri' => Utils::normalizeUrl($baseUrl),
            'handler'  => $stack,
        ]);
    }

    private function mergeOptions(array $serviceOptions)
    {
        $options = array_merge($this->defaults, $this->globalOptions, $serviceOptions);

        if (!isset($options['authUrl'])) {
            throw new \InvalidArgumentException('"authUrl" is a required option');
        }

        return $options;
    }
}
