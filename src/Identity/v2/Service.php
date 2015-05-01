<?php

namespace OpenStack\Identity\v2;

use OpenStack\Common\Service\AbstractService;

/**
 * Represents the OpenStack Identity v2 service.
 *
 * @property \OpenStack\Identity\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * Generates a new authentication token
     *
     * @param array $options {@see \OpenStack\Identity\v2\Api::postToken}
     *
     * @return Models\Token
     */
    public function generateToken(array $options = [])
    {
        $response = $this->execute($this->api->postToken(), $options);
        return $this->model('Token', $response);
    }
}