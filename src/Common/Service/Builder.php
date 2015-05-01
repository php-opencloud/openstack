<?php

namespace OpenStack\Common\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use OpenStack\Common\Auth\AuthHandler;
use OpenStack\Common\Auth\ServiceUrlResolver;

/**
 * A Builder for easily creating OpenStack services.
 *
 * @package OpenStack\Common\Service
 */
class Builder
{
    /** @var array */
    private $globalOptions = [];

    /** @var array */
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
     * @param $serviceVersion       The version as an integer, which will be prepended with a v
     * @param array $serviceOptions The service-specific options to use
     * @return mixed                OpenStack\Common\Service\ServiceInterface
     * @throws \Exception
     */
    public function createService($serviceName, $serviceVersion, array $serviceOptions = [])
    {
        $options = array_merge($this->defaults, $this->globalOptions, $serviceOptions);
        $this->checkRequiredOptions($options);

        list ($apiClass, $serviceClass) = $this->getClasses($serviceName, $serviceVersion);

        return new $serviceClass($this->setupHttpClient($options), new $apiClass());
    }

    public function createIdentityService($serviceVersion, array $serviceOptions = [])
    {
        $options = array_merge($this->defaults, $this->globalOptions, $serviceOptions);

        list ($apiClass, $serviceClass) = $this->getClasses('Identity', $serviceVersion);

        return new $serviceClass($this->httpClient($options['authUrl'], $options), new $apiClass());
    }

    /**
     * This method does a few different things, but the overall purpose is to return a suitable
     * HTTP client which can be injected into an OpenStack service.
     *
     * The first thing that happens is to use the KeyStone v2 Service to generate a token. This
     * also causes a Service Catalog to be returned.
     *
     * The service URL is passed in to the HTTP client as its base URL. The authentication handler
     * is then attached to the HTTP client as an event subscriber, meaning that it will listen out
     * for an event to be fired before every Request is sent. It is given an initial token.
     *
     * @param array $options
     * @return Client
     */
    private function setupHttpClient(array $options)
    {
        $httpClient = isset($options['httpClient'])
            ? $options['httpClient']
            : $this->httpClient($options['authUrl'], $options);

        $resolver = new ServiceUrlResolver($httpClient);
        $resolver->resolve($options);

        $httpClient = $this->httpClient($resolver->getServiceUrl(), $options);
        $httpClient->getEmitter()->attach(new AuthHandler($resolver->getService(), $options, $resolver->getToken()));
        return $httpClient;
    }

    public function httpClient($baseUrl, array $options = [])
    {
        $client = new Client([
            'base_url' => rtrim($baseUrl, '/') . '/',
        ]);

        if (isset($options['debug']) && $options['debug'] === true) {
            $client->getEmitter()->attach(new LogSubscriber(null, Formatter::DEBUG));
        }

        return $client;
    }

    /**
     * Ensures that user-provided input contains required keys.
     *
     * @param array $options
     * @throws \Exception    If not all required keys are provided
     */
    private function checkRequiredOptions(array $options)
    {
        $failures = [];

        foreach (['username', 'password', 'authUrl', 'region', 'catalogName', 'catalogType'] as $requiredOption) {
            if (!isset($options[$requiredOption])) {
                $failures[] = $requiredOption;
            }
        }

        if (!empty($failures)) {
            throw new \Exception(sprintf("You must provide these options: %s", implode(', ', $failures)));
        }

        if (!isset($options['tenantId']) && !isset($options['tenantName'])) {
            throw new \Exception('You must provide either a tenantId or tenantName');
        }
    }
}