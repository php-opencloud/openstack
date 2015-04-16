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

    public function listFlavors(array $options = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($this->api->getFlavors(), $options);
        return $this->model('Flavor')->enumerate($operation, $mapFn);
    }

    public function getFlavor(array $options = [])
    {
        $flavor = $this->model('Flavor');
        $flavor->populateFromArray($options);
        return $flavor;
    }

    public function listImages(array $options = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($this->api->getImages(), $options);
        return $this->model('Image')->enumerate($operation, $mapFn);
    }

    public function getImage(array $options = [])
    {
        $image = $this->model('Image');
        $image->populateFromArray($options);
        return $image;
    }
}