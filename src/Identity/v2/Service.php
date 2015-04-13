<?php

namespace OpenStack\Identity\v2;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Identity\v2\Api\Token as TokenApi;

class Service extends AbstractService
{
    public function generateToken(array $options = [])
    {
        $response = $this->execute(TokenApi::post(), $options);
        return $this->model('Token', $response);
    }
}