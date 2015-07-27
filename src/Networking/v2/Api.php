<?php

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

    public function getNetwork()
    {
        return [
            'method' => 'GET',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'params' => ['id' => $this->params->urlId('network')],
        ];
    }

    public function postNetwork()
    {
        return [
            'path' => $this->pathPrefix . '/networks',
            'method' => 'POST',
            'jsonKey' => 'network',
            'params' => [
                'name' => $this->params->name('network'),
                'shared' => $this->params->shared(),
                'adminStateUp' => $this->params->adminStateUp(),
            ]
        ];
    }

    public function postNetworks()
    {
        return [
            'path' => $this->pathPrefix . '/networks',
            'method' => 'POST',
            'jsonKey' => '',
            'params' => [
                'networks' => [
                    'type' => 'array',
                    'description' => 'List of networks',
                    'items' => [
                        'type'       => 'object',
                        'properties' => [
                            'name' => $this->params->name('network'),
                            'shared' => $this->params->shared(),
                            'adminStateUp' => $this->params->adminStateUp(),
                        ]
                    ],
                ]
            ]
        ];
    }

    public function putNetwork()
    {
        return [
            'method' => 'PUT',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'jsonKey' => 'network',
            'params' => [
                'id' => $this->params->urlId('network'),
                'name' => $this->params->name('network'),
                'shared' => $this->params->shared(),
                'adminStateUp' => $this->params->adminStateUp(),
            ],
        ];
    }

    public function deleteNetwork()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/networks/{id}',
            'params' => ['id' => $this->params->urlId('network')]
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

    public function postSubnet()
    {
        return [
            'path' => $this->pathPrefix . '/subnets',
            'method' => 'POST',
            'jsonKey' => 'subnet',
            'params' => [
                'name' => $this->params->name('subnet'),
                'networkId' => $this->isRequired($this->params->networkId()),
                'ipVersion' => $this->isRequired($this->params->ipVersion()),
                'cidr' => $this->isRequired($this->params->cidr()),
                'tenantId' => $this->params->tenantId(),
                'gatewayIp' => $this->params->gatewayIp(),
                'enableDhcp' => $this->params->enableDhcp(),
                'dnsNameservers' => $this->params->dnsNameservers(),
                'allocationPools' => $this->params->allocationPools(),
                'hostRoutes' => $this->params->hostRoutes(),
            ]
        ];
    }

    public function postSubnets()
    {
        return [
            'path' => $this->pathPrefix . '/subnets',
            'method' => 'POST',
            'jsonKey' => '',
            'params' => [
                'subnets' => [
                    'type' => 'array',
                    'description' => 'List of subnets',
                    'items' => [
                        'type'       => 'object',
                        'properties' => [
                            'name' => $this->params->name('subnet'),
                            'networkId' => $this->isRequired($this->params->networkId()),
                            'ipVersion' => $this->isRequired($this->params->ipVersion()),
                            'cidr' => $this->isRequired($this->params->cidr()),
                            'tenantId' => $this->params->tenantId(),
                            'gatewayIp' => $this->params->gatewayIp(),
                            'enableDhcp' => $this->params->enableDhcp(),
                            'dnsNameservers' => $this->params->dnsNameservers(),
                            'allocationPools' => $this->params->allocationPools(),
                            'hostRoutes' => $this->params->hostRoutes(),
                        ]
                    ],
                ]
            ]
        ];
    }

    public function putSubnet()
    {
        return [
            'method' => 'PUT',
            'path'   => $this->pathPrefix . '/subnets/{id}',
            'jsonKey' => 'subnet',
            'params' => [
                'id' => $this->params->urlId('subnet'),
                'name' => $this->params->name('subnet'),
                'gatewayIp' => $this->params->gatewayIp(),
                'dnsNameservers' => $this->params->dnsNameservers(),
                'allocationPools' => $this->params->allocationPools(),
                'hostRoutes' => $this->params->hostRoutes(),
            ],
        ];
    }

    public function deleteSubnet()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->pathPrefix . '/subnets/{id}',
            'params' => ['id' => $this->params->urlId('subnet')]
        ];
    }
}
