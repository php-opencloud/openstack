<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Service\AbstractService;

class Service extends AbstractService
{
    public function createServer($name, $imageId, $flavorId, array $options = [])
    {
        $options = array_merge($options, ['name' => $name, 'flavorId' => $flavorId, 'imageId' => $imageId]);

        return $this->model('Server')->create($options);
    }
}