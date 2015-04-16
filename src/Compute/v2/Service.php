<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Service\AbstractService;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * @param array $options
     *
     * @return Models\Server
     */
    public function createServer(array $options)
    {
        return $this->model('Server')->create($options);
    }

    public function listServers(array $options = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($this->api->getServers(), $options);
        return $this->model('Server')->enumerate($operation, $mapFn);
    }

    public function getServer(array $options = [])
    {
        $server = $this->model('Server');
        $server->populateFromArray($options);
        return $server;
    }

    public function listFlavors(array $options = [])
    {

    }

    public function getFlavor(array $options = [])
    {

    }

    public function listImages(array $options = [])
    {

    }

    public function getImage(array $options = [])
    {

    }
}