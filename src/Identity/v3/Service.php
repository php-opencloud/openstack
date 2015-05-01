<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Common\Service\Builder;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Service extends AbstractService
{
    /**
     * Generates a new authentication token
     *
     * @param array $options {@see \OpenStack\Identity\v3\Api::postTokens}
     *
     * @return Models\Token
     */
    public function generateToken(array $options)
    {
        return $this->model('Token')->create($options);
    }
}