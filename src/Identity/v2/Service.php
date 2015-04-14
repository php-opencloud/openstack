<?php

namespace OpenStack\Identity\v2;

use OpenStack\Common\Service\AbstractService;

class Service extends AbstractService
{
    public function generateToken(array $options = [])
    {
        $response = $this->execute(Api::postToken(), $options);
        return $this->model('Token', $response);
    }
}