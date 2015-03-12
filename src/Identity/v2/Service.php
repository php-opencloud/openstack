<?php

namespace OpenStack\Identity\v2;

use OpenStack\Common\Service\AbstractService;

class Service extends AbstractService
{
    public function generateTokenAndServiceUrl(array $options)
    {
        $authOpts = ['username' => null, 'password' => null, 'tenantId' => null, 'tenantName' => null];
        $response = $this->execute('postTokens', array_intersect_key($options, $authOpts));

        $serviceUrl = $this->model('Catalog', $response)->getEndpointUrl(
            $options['catalogName'],
            $options['catalogType'],
            $options['region'],
            $options['urlType']
        );

        return [$serviceUrl, $this->model('Token', $response)];
    }

    public function generateToken($username, $password, $tenantName, array $options = [])
    {
        $options = array_merge($options, ['username' => $username, 'password' => $password]);

        if (isset($options['tenantId']) && !$tenantName) {
            $options['tenantId'] = $options['tenantId'];
        } else {
            $options['tenantName'] = $tenantName;
        }

        $response = $this->execute('postTokens', $options);

        return $this->model('Token', $response);
    }
}