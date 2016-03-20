<?php declare (strict_types=1);

namespace OpenStack;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use OpenCloud\Common\Service\Builder;
use OpenCloud\Common\Transport\Utils;
use OpenStack\Identity\v3\Service;

/**
 * This class is the primary entry point for working with the SDK. It allows for the easy creation
 * of OpenStack services.
 *
 * @package OpenStack
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

        return Service::factory(new Client([
            'base_uri' => Utils::normalizeUrl($options['authUrl']),
            'handler'  => HandlerStack::create(),
        ]));
    }

    /**
     * Creates a new Compute v2 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\Compute\v2\Service
     */
    public function computeV2(array $options = []): \OpenStack\Compute\v2\Service
    {
        $defaults = ['catalogName' => 'nova', 'catalogType' => 'compute'];
        return $this->builder->createService('Compute', 2, array_merge($defaults, $options));
    }

    /**
     * Creates a new Networking v2 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\Networking\v2\Service
     */
    public function networkingV2(array $options = []): \OpenStack\Networking\v2\Service
    {
        $defaults = ['catalogName' => 'neutron', 'catalogType' => 'network'];
        return $this->builder->createService('Networking', 2, array_merge($defaults, $options));
    }

    /**
     * Creates a new Identity v2 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\Identity\v2\Service
     */
    public function identityV2(array $options = []): \OpenStack\Identity\v2\Service
    {
        $defaults = ['catalogName' => false, 'catalogType' => false];
        return $this->builder->createService('Identity', 2, array_merge($defaults, $options));
    }

    /**
     * Creates a new Identity v3 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\Identity\v3\Service
     */
    public function identityV3(array $options = []): \OpenStack\Identity\v3\Service
    {
        $defaults = ['catalogName' => false, 'catalogType' => false];
        return $this->builder->createService('Identity', 3, array_merge($defaults, $options));
    }

    /**
     * Creates a new Object Store v1 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\ObjectStore\v1\Service
     */
    public function objectStoreV1(array $options = []): \OpenStack\ObjectStore\v1\Service
    {
        $defaults = ['catalogName' => 'swift', 'catalogType' => 'object-store'];
        return $this->builder->createService('ObjectStore', 1, array_merge($defaults, $options));
    }

    /**
     * Creates a new Block Storage v2 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\BlockStorage\v2\Service
     */
    public function blockStorageV2(array $options = []): \OpenStack\BlockStorage\v2\Service
    {
        $defaults = ['catalogName' => 'cinderv2', 'catalogType' => 'volumev2'];
        return $this->builder->createService('BlockStorage', 2, array_merge($defaults, $options));
    }

    /**
     * Creates a new Images v2 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\Images\v2\Service
     */
    public function imagesV2(array $options = []): \OpenStack\Images\v2\Service
    {
        $defaults = ['catalogName' => 'glance', 'catalogType' => 'image'];
        return $this->builder->createService('Images', 2, array_merge($defaults, $options));
    }
}
