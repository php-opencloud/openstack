<?php

namespace OpenStack\Identity\v2;

use OpenStack\Common\Service\AbstractService;

/**
 * @property \OpenStack\Identity\v2\Api $api
 */
class Service extends AbstractService
{
    public function generateToken(array $options = [])
    {
        $response = $this->execute($this->api->postToken(), $options);
        return $this->model('Token', $response);
    }
}