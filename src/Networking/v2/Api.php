<?php declare(strict_types=1);

namespace OpenStack\Networking\v2;

use OpenStack\Common\Api\AbstractApi;

/**
 * A representation of the Neutron (Nova) v2 REST API.
 *
 * @internal
 * @package OpenStack\Networking\v2
 */
class Api extends AbstractApi
{
    private $pathPrefix = 'v2.0';

    public function __construct()
    {
        $this->params = new Params();
    }

    public function getNetwork(): array
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function getNetworks(): array
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/networks',
            'params' => [
                'name'     => $this->params->queryName(),
                'tenantId' => $this->params->queryTenantId(),
                'status'   => $this->params->queryStatus(),
            ],
        ];
    }

    public function postNetwork(): array
    {
        return [
            'path'    => $this->pathPrefix . '/networks',
            'method'  => 'POST',
            'jsonKey' => 'network',
            'params'  => [
                'name'             => $this->params->name('network'),
                'shared'           => $this->params->shared(),
                'adminStateUp'     => $this->params->adminStateUp(),
                'routerAccessible' => $this->params->routerAccessibleJson(),
                'tenantId'         => $this->params->tenantId(),
            ],
        ];
    }

    public function postNetworks(): array
    {
        return [
            'path'    => $this->pathPrefix . '/networks',
            'method'  => 'POST',
            'jsonKey' => '',
            'params'  => [
                'networks' => [
                    'type'        => 'array',
                    'description' => 'List of networks',
                    'items'       => [
                        'type'       => 'object',
                        'properties' => [
                            'name'         => $this->params->name('network'),
                            'shared'       => $this->params->shared(),
                            'adminStateUp' => $this->params->adminStateUp(),
                        ],
                    ],
                ],
            ],
        ];
    }

    public function putNetwork(): array
    {
        return [
            'method'  => 'PUT',
            'path'    => $this->pathPrefix . '/networks/{id}',
            'jsonKey' => 'network',
            'params'  => [
                'id'           => $this->params->urlId('network'),
                'name'         => $this->params->name('network'),
                'shared'       => $this->params->shared(),
                'adminStateUp' => $this->params->adminStateUp(),
            ],
        ];
    }

    public function deleteNetwork(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function getSubnet(): array
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/subnets/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function getSubnets(): array
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/subnets',
            'params' => [
                'name' => $this->params->queryName(),
                'tenantId' => $this->params->queryTenantId()
            ],
        ];
    }

    public function postSubnet(): array
    {
        return [
            'path'    => $this->pathPrefix . '/subnets',
            'method'  => 'POST',
            'jsonKey' => 'subnet',
            'params'  => [
                'name'            => $this->params->name('subnet'),
                'networkId'       => $this->isRequired($this->params->networkId()),
                'ipVersion'       => $this->isRequired($this->params->ipVersion()),
                'cidr'            => $this->isRequired($this->params->cidr()),
                'tenantId'        => $this->params->tenantId(),
                'gatewayIp'       => $this->params->gatewayIp(),
                'enableDhcp'      => $this->params->enableDhcp(),
                'dnsNameservers'  => $this->params->dnsNameservers(),
                'allocationPools' => $this->params->allocationPools(),
                'hostRoutes'      => $this->params->hostRoutes(),
            ],
        ];
    }

    public function postSubnets(): array
    {
        return [
            'path'   => $this->pathPrefix . '/subnets',
            'method' => 'POST',
            'params' => [
                'subnets' => [
                    'type'        => Params::ARRAY_TYPE,
                    'description' => 'List of subnets',
                    'items'       => [
                        'type'       => Params::OBJECT_TYPE,
                        'properties' => $this->postSubnet()['params'],
                    ],
                ],
            ],
        ];
    }

    public function putSubnet(): array
    {
        return [
            'method'  => 'PUT',
            'path'    => $this->pathPrefix . '/subnets/{id}',
            'jsonKey' => 'subnet',
            'params'  => [
                'id'              => $this->params->urlId('subnet'),
                'name'            => $this->params->name('subnet'),
                'gatewayIp'       => $this->params->gatewayIp(),
                'dnsNameservers'  => $this->params->dnsNameservers(),
                'allocationPools' => $this->params->allocationPools(),
                'hostRoutes'      => $this->params->hostRoutes(),
            ],
        ];
    }

    public function deleteSubnet(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/subnets/{id}',
            'params' => ['id' => $this->params->urlId('subnet')],
        ];
    }

    public function getPorts(): array
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/ports',
            'params' => [
                'status'         => $this->params->statusQuery(),
                'displayName'    => $this->params->displayNameQuery(),
                'adminState'     => $this->params->adminStateQuery(),
                'networkId'      => $this->notRequired($this->params->networkId()),
                'tenantId'       => $this->params->tenantId(),
                'deviceOwner'    => $this->params->deviceOwnerQuery(),
                'macAddress'     => $this->params->macAddrQuery(),
                'portId'         => $this->params->portIdQuery(),
                'securityGroups' => $this->params->secGroupsQuery(),
                'deviceId'       => $this->params->deviceIdQuery(),
            ],
        ];
    }

    public function postSinglePort(): array
    {
        return [
            'method'  => 'POST',
            'path'    => $this->pathPrefix . '/ports',
            'jsonKey' => 'port',
            'params'  => [
                'name'                => $this->params->name('port'),
                'adminStateUp'        => $this->params->adminStateUp(),
                'tenantId'            => $this->params->tenantId(),
                'macAddress'          => $this->params->macAddr(),
                'fixedIps'            => $this->params->fixedIps(),
                'subnetId'            => $this->params->subnetId(),
                'ipAddress'           => $this->params->ipAddress(),
                'securityGroups'      => $this->params->secGroupIds(),
                'networkId'           => $this->params->networkId(),
                'allowedAddressPairs' => $this->params->allowedAddrPairs(),
                'deviceOwner'         => $this->params->deviceOwner(),
                'deviceId'            => $this->params->deviceId(),
            ],
        ];
    }

    public function postMultiplePorts(): array
    {
        return [
            'method' => 'POST',
            'path'   => $this->pathPrefix . '/ports',
            'params' => [
                'ports' => [
                    'type'  => Params::ARRAY_TYPE,
                    'items' => [
                        'type'       => Params::OBJECT_TYPE,
                        'properties' => $this->postSinglePort()['params'],
                    ],
                ],
            ],
        ];
    }

    public function getPort(): array
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/ports/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }

    public function putPort(): array
    {
        return [
            'method'  => 'PUT',
            'path'    => $this->pathPrefix . '/ports/{id}',
            'jsonKey' => 'port',
            'params'  => [
                'id'                  => $this->params->idPath(),
                'name'                => $this->params->name('port'),
                'adminStateUp'        => $this->params->adminStateUp(),
                'tenantId'            => $this->params->tenantId(),
                'macAddress'          => $this->params->macAddr(),
                'fixedIps'            => $this->params->fixedIps(),
                'subnetId'            => $this->params->subnetId(),
                'ipAddress'           => $this->params->ipAddress(),
                'securityGroups'      => $this->params->secGroupIds(),
                'networkId'           => $this->notRequired($this->params->networkId()),
                'allowedAddressPairs' => $this->params->allowedAddrPairs(),
                'deviceOwner'         => $this->params->deviceOwner(),
                'deviceId'            => $this->params->deviceId(),
            ],
        ];
    }

    public function deletePort(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/ports/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }
}
