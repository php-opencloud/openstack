<?php

declare(strict_types=1);

namespace OpenStack;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware as GuzzleMiddleware;
use OpenStack\Common\Service\Builder;
use OpenStack\Common\Transport\Utils;
use OpenStack\Identity\v3\Service;

/**
 * This class is the primary entry point for working with the SDK. It allows for the easy creation
 * of OpenStack services.
 */
class OpenStack
{
    /** @var Builder */
    private $builder;

    /**
     * @param array $options User-defined options
     *
     * $options['username']         = (string)            Your OpenStack username        [REQUIRED]
     *         ['password']         = (string)            Your OpenStack password        [REQUIRED]
     *         ['tenantId']         = (string)            Your tenant ID                 [REQUIRED if tenantName omitted]
     *         ['tenantName']       = (string)            Your tenant name               [REQUIRED if tenantId omitted]
     *         ['authUrl']          = (string)            The Keystone URL               [REQUIRED]
     *         ['debugLog']         = (bool)              Whether to enable HTTP logging [OPTIONAL]
     *         ['logger']           = (LoggerInterface)   Must set if debugLog is true   [OPTIONAL]
     *         ['messageFormatter'] = (MessageFormatter)  Must set if debugLog is true   [OPTIONAL]
     *         ['requestOptions']   = (array)             Guzzle Http request options    [OPTIONAL]
     *         ['cachedToken']      = (array)             Cached token credential        [OPTIONAL]
     * @param Builder $builder
     */
    public function __construct(array $options = [], Builder $builder = null)
    {
        if (!isset($options['identityService'])) {
            $options['identityService'] = $this->getDefaultIdentityService($options);
        }

        $this->builder = $builder ?: new Builder($options, 'OpenStack');
    }

    /**
     * @param array $options
     *
     * @return Service
     */
    private function getDefaultIdentityService(array $options): Service
    {
        if (!isset($options['authUrl'])) {
            throw new \InvalidArgumentException("'authUrl' is a required option");
        }

        $stack = HandlerStack::create();

        if (!empty($options['debugLog'])
            && !empty($options['logger'])
            && !empty($options['messageFormatter'])
        ) {
            $stack->push(GuzzleMiddleware::log($options['logger'], $options['messageFormatter']));
        }

        $clientOptions = [
            'base_uri' => Utils::normalizeUrl($options['authUrl']),
            'handler'  => $stack,
        ];

        if (isset($options['requestOptions'])) {
            $clientOptions = array_merge($options['requestOptions'], $clientOptions);
        }

        return Service::factory(new Client($clientOptions));
    }

    /**
     * Creates a new Compute v2 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Compute\v2\Service
     */
    public function computeV2(array $options = []): \OpenStack\Compute\v2\Service
    {
        $defaults = ['catalogName' => 'nova', 'catalogType' => 'compute'];

        return $this->builder->createService('Compute\\v2', array_merge($defaults, $options));
    }

    /**
     * Creates a new Networking v2 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Networking\v2\Service
     */
    public function networkingV2(array $options = []): \OpenStack\Networking\v2\Service
    {
        $defaults = ['catalogName' => 'neutron', 'catalogType' => 'network'];

        return $this->builder->createService('Networking\\v2', array_merge($defaults, $options));
    }

    /**
     * Creates a new Networking v2 Layer 3 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Networking\v2\Extensions\Layer3\Service
     */
    public function networkingV2ExtLayer3(array $options = []): \OpenStack\Networking\v2\Extensions\Layer3\Service
    {
        $defaults = ['catalogName' => 'neutron', 'catalogType' => 'network'];

        return $this->builder->createService('Networking\\v2\\Extensions\\Layer3', array_merge($defaults, $options));
    }

    /**
     * Creates a new Networking v2 Layer 3 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Networking\v2\Extensions\SecurityGroups\Service
     */
    public function networkingV2ExtSecGroups(array $options = []): \OpenStack\Networking\v2\Extensions\SecurityGroups\Service
    {
        $defaults = ['catalogName' => 'neutron', 'catalogType' => 'network'];

        return $this->builder->createService('Networking\\v2\\Extensions\\SecurityGroups', array_merge($defaults, $options));
    }

    /**
     * Creates a new Identity v2 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Identity\v2\Service
     */
    public function identityV2(array $options = []): \OpenStack\Identity\v2\Service
    {
        $defaults = ['catalogName' => 'keystone', 'catalogType' => 'identity'];

        return $this->builder->createService('Identity\\v2', array_merge($defaults, $options));
    }

    /**
     * Creates a new Identity v3 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Identity\v3\Service
     */
    public function identityV3(array $options = []): \OpenStack\Identity\v3\Service
    {
        $defaults = ['catalogName' => 'keystone', 'catalogType' => 'identity'];

        return $this->builder->createService('Identity\\v3', array_merge($defaults, $options));
    }

    /**
     * Creates a new Object Store v1 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\ObjectStore\v1\Service
     */
    public function objectStoreV1(array $options = []): \OpenStack\ObjectStore\v1\Service
    {
        $defaults = ['catalogName' => 'swift', 'catalogType' => 'object-store'];

        return $this->builder->createService('ObjectStore\\v1', array_merge($defaults, $options));
    }

    /**
     * Creates a new Block Storage v2 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\BlockStorage\v2\Service
     */
    public function blockStorageV2(array $options = []): \OpenStack\BlockStorage\v2\Service
    {
        $defaults = ['catalogName' => 'cinderv2', 'catalogType' => 'volumev2'];

        return $this->builder->createService('BlockStorage\\v2', array_merge($defaults, $options));
    }

    /**
     * Creates a new Images v2 service.
     *
     * @param array $options options that will be used in configuring the service
     *
     * @return \OpenStack\Images\v2\Service
     */
    public function imagesV2(array $options = []): \OpenStack\Images\v2\Service
    {
        $defaults = ['catalogName' => 'glance', 'catalogType' => 'image'];

        return $this->builder->createService('Images\\v2', array_merge($defaults, $options));
    }

    /**
     * Creates a new Gnocchi Metric service v1.
     *
     * @param array $options
     *
     * @return \OpenStack\Metric\v1\Gnocchi\Service
     */
    public function metricGnocchiV1(array $options = []): \OpenStack\Metric\v1\Gnocchi\Service
    {
        $defaults = ['catalogName' => 'gnocchi', 'catalogType' => 'metric'];

        return $this->builder->createService('Metric\\v1\\Gnocchi', array_merge($defaults, $options));
    }
}
