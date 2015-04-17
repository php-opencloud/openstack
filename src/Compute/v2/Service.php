<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Service\AbstractService;

/**
 * Compute v2 service for OpenStack.
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * Create a new server resource. This operation will provision a new virtual machine on a host chosen by your
     * service API.
     *
     * @param array $options {@see \OpenStack\Compute\v2\Api::postServer}
     *
     * @return \OpenStack\Compute\v2\Models\Server
     */
    public function createServer(array $options)
    {
        return $this->model('Server')->create($options);
    }

    /**
     * List servers.
     *
     * @param array    $options {@see Api::getServers}
     * @param callable $mapFn   A callable function that will be invoked on every iteration of the list.
     *
     * @return \Generator
     */
    public function listServers(array $options = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($this->api->getServers(), $options);
        return $this->model('Server')->enumerate($operation, $mapFn);
    }

    /**
     * Retrieve a server object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Server::retrieve}.
     *
     * @param array $options An array of attributes that will be set on the {@see Server} object. The array keys need to
     *                       correspond to the class public properties.
     *
     * @return \OpenStack\Compute\v2\Models\Server
     */
    public function getServer(array $options = [])
    {
        $server = $this->model('Server');
        $server->populateFromArray($options);
        return $server;
    }

    /**
     * List flavors.
     *
     * @param array    $options {@see Api::getFlavors}
     * @param callable $mapFn   A callable function that will be invoked on every iteration of the list.
     *
     * @return \Generator
     */
    public function listFlavors(array $options = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($this->api->getFlavors(), $options);
        return $this->model('Flavor')->enumerate($operation, $mapFn);
    }

    /**
     * Retrieve a flavor object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Flavor::retrieve}.
     *
     * @param array $options An array of attributes that will be set on the {@see Flavor} object. The array keys need to
     *                       correspond to the class public properties.
     *
     * @return \OpenStack\Compute\v2\Models\Flavor
     */
    public function getFlavor(array $options = [])
    {
        $flavor = $this->model('Flavor');
        $flavor->populateFromArray($options);
        return $flavor;
    }

    /**
     * List images.
     *
     * @param array    $options {@see Api::getImages}
     * @param callable $mapFn   A callable function that will be invoked on every iteration of the list.
     *
     * @return \Generator
     */
    public function listImages(array $options = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($this->api->getImages(), $options);
        return $this->model('Image')->enumerate($operation, $mapFn);
    }

    /**
     * Retrieve an image object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Image::retrieve}.
     *
     * @param array $options An array of attributes that will be set on the {@see Image} object. The array keys need to
     *                       correspond to the class public properties.
     *
     * @return \OpenStack\Compute\v2\Models\Image
     */
    public function getImage(array $options = [])
    {
        $image = $this->model('Image');
        $image->populateFromArray($options);
        return $image;
    }
}