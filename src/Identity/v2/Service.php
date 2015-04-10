<?php

namespace OpenStack\Identity\v2;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Identity\v2\Api\Token as TokenApi;

class Service extends AbstractService
{
    public function generateToken($username, $password, $tenantName, array $options = [])
    {
        $options = array_merge($options, ['username' => $username, 'password' => $password]);

        if (isset($options['tenantId']) && !$tenantName) {
            $options['tenantId'] = $options['tenantId'];
        } else {
            $options['tenantName'] = $tenantName;
        }

        $response = $this->execute(TokenApi::post(), $options);

        return $this->model('Token', $response);
    }
}