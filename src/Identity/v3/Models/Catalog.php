<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Catalog extends AbstractResource implements \OpenStack\Common\Auth\Catalog
{
    /** @var []Service */
    public $services;

    public function populateFromArray(array $data)
    {
        foreach ($data as $service) {
            $this->services[] = $this->model(Service::class, $service);
        }
    }

    /**
     * Retrieve a base URL for a service, according to its catalog name, type, region.
     *
     * @param string $name    The name of the service as it appears in the catalog.
     * @param string $type    The type of the service as it appears in the catalog.
     * @param string $region  The region of the service as it appears in the catalog.
     * @param string $urlType Unused.
     *
     * @return false|string   FALSE if no URL found
     */
    public function getServiceUrl($name, $type, $region, $urlType)
    {
        if (empty($this->services)) {
            return false;
        }

        foreach ($this->services as $service) {
            if (false !== ($url = $service->getUrl($name, $type, $region, $urlType))) {
                return $url;
            }
        }

        return false;
    }
}
