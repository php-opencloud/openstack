<?php

namespace OpenStack\Networking\v2\Extensions\Layer3;

use OpenCloud\Common\Service\AbstractService;
use OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp;
use OpenStack\Networking\v2\Extensions\Layer3\Models\Router;

/**
 * @property Api $api
 */
class Service extends AbstractService
{
    private function floatingIp(array $info = []): FloatingIp
    {
        return $this->model(FloatingIp::class, $info);
    }

    private function router(array $info = []): Router
    {
        return $this->model(Router::class, $info);
    }

    /**
     * @param array $options
     *
     * @return FloatingIp
     */
    public function createFloatingIp(array $options): FloatingIp
    {
        return $this->floatingIp()->create($options);
    }

    /**
     * @return FloatingIp
     */
    public function getFloatingIp($id): FloatingIp
    {
        return $this->floatingIp(['id' => $id]);
    }

    /**
     * @return \Generator
     */
    public function listFloatingIps(): \Generator
    {
        return $this->floatingIp()->enumerate($this->api->getFloatingIps());
    }

    /**
     * @param array $options
     *
     * @return Router
     */
    public function createRouter(array $options): Router
    {
        return $this->router()->create($options);
    }

    /**
     * @return Router
     */
    public function getRouter($id): Router
    {
        return $this->router(['id' => $id]);
    }

    /**
     * @return \Generator
     */
    public function listRouters(): \Generator
    {
        return $this->router()->enumerate($this->api->getRouters());
    }
}
