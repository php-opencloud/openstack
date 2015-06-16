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

    public function getServiceUrl($name, $type, $region, $urlType)
    {
        foreach ($this->service as $service) {
            if (false !== ($url = $service->getUrl($name, $type, $region))) {
                return $url;
            }
        }

        return false;
    }
}