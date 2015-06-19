<?php

namespace OpenStack\Networking\v2;

use OpenStack\Common\Service\AbstractService;

/**
 * Network v2 service for OpenStack.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * Create a new network resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postNetwork}
     *
     * @return \OpenStack\Networking\v2\Models\Network
     */
    public function createNetwork(array $options)
    {
        return $this->model('Network')->create($options);
    }

    /**
     * Retrieve a network object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Server::retrieve}.
     *
     * @param array $options An array of attributes that will be set on the {@see Network} object. The array keys need to
     *                       correspond to the class public properties.
     *
     * @return \OpenStack\Networking\v2\Models\Network
     */
    public function getNetwork(array $options = [])
    {
        $server = $this->model('Network');
        $server->populateFromArray($options);
        return $server;
    }
}
