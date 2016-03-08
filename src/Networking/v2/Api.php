<?php declare(strict_types=1);

namespace OpenStack\Networking\v2;

use OpenCloud\Common\Api\AbstractApi;

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

    public function getNetwork()
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function getNetworks()
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/networks',
            'params' => [],
        ];
    }

    public function postNetwork()
    {
        return [
            'path'    => $this->pathPrefix . '/networks',
            'method'  => 'POST',
            'jsonKey' => 'network',
            'params'  => [
                'name'         => $this->params->name('network'),
                'shared'       => $this->params->shared(),
                'adminStateUp' => $this->params->adminStateUp(),
            ],
        ];
    }

    public function postNetworks()
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

    public function putNetwork()
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

    public function deleteNetwork()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function getSubnet()
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/subnets/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function getSubnets()
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/subnets',
            'params' => [],
        ];
    }

    public function postSubnet()
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

    public function postSubnets()
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

    public function putSubnet()
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

    public function deleteSubnet()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/subnets/{id}',
            'params' => ['id' => $this->params->urlId('subnet')],
        ];
    }

    public function getPorts()
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

    public function postSinglePort()
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

    public function postMultiplePorts()
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

    public function getPort()
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/ports/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }

    public function putPort()
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

    public function deletePort()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/ports/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }
}
