<?php

namespace OpenStack;

use OpenStack\Common\Service\Builder;

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
     * $options['username']   = (string) Your OpenStack username        [REQUIRED]
     *         ['password']   = (string) Your OpenStack password        [REQUIRED]
     *         ['tenantId']   = (string) Your tenant ID                 [REQUIRED if tenantName omitted]
     *         ['tenantName'] = (string) Your tenant name               [REQUIRED if tenantId omitted]
     *         ['authUrl']    = (string) The Keystone URL               [REQUIRED]
     *         ['debug']      = (bool)   Whether to enable HTTP logging [OPTIONAL]
     */
    public function __construct(array $options = [], Builder $builder = null)
    {
        $this->builder = $builder ?: new Builder($options);
    }

    /**
     * Creates a new Compute v2 service.
     *
     * @param array $options Options that will be used in configuring the service.
     *
     * @return \OpenStack\Compute\v2\Service
     */
    public function computeV2(array $options = [])
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
    public function networkingV2(array $options = [])
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
    public function identityV2(array $options = [])
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
    public function identityV3(array $options = [])
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
    public function objectStoreV1(array $options = [])
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
    public function blockStorageV2(array $options = [])
    {
        $defaults = ['catalogName' => 'cinderv2', 'catalogType' => 'volumev2'];
        return $this->builder->createService('BlockStorage', 2, array_merge($defaults, $options));
    }
}
