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
     * @param array $options Supported options:
     *
     * username   (string) Your OpenStack username [REQUIRED]
     * password   (string) Your OpenStack password [REQUIRED]
     * tenantId   (string) Your tenant ID          [either tenantId or tenantName must be required]
     * tenantName (string) Your tenant name        [either tenantId or tenantName must be required]
     * authUrl    (string) The Keystone URL        [REQUIRED]
     * debug      (bool)   Whether to enable HTTP logging [OPTIONAL]
     */
    public function __construct(array $options = [])
    {
        $options += $this->getEnvVars();

        $this->builder = new Builder($options);
    }

    private function getEnvVars()
    {
        return [
            'username'   => getenv('OS_USERNAME'),
            'password'   => getenv('OS_PASSWORD'),
            'tenantId'   => getenv('OS_TENANT_ID'),
            'tenantName' => getenv('OS_TENANT_NAME'),
            'authUrl'    => getenv('OS_AUTH_URL'),
        ];
    }

    /**
     * @param array $options
     * @return \OpenStack\ObjectStore\v2\Service
     */
    public function objectStoreV2(array $options = [])
    {
        $options = array_merge($options, ['catalogName' => 'swift', 'catalogType' => 'object-store']);
        return $this->builder->createService('ObjectStore', 2, $options);
    }

    /**
     * @param array $options
     * @return \OpenStack\Compute\v2\Service
     */
    public function computeV2(array $options = [])
    {
        $options = array_merge($options, ['catalogName' => 'nova', 'catalogType' => 'compute']);
        return $this->builder->createService('Compute', 2, $options);
    }
}