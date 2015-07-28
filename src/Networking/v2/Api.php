<?php

namespace OpenStack\Networking\v2;

use OpenStack\Common\Api\ApiInterface;

/**
 * A representation of the Neutron (Nova) v2 REST API.
 *
 * @internal
 * @package OpenStack\Networking\v2
 */
class Api implements ApiInterface
{
    public function __construct()
    {
        $this->params = new Params();
    }

    private $pathPrefix = 'v2.0';

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
}
