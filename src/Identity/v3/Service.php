<?php

namespace OpenStack\Identity\v3;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Common\Service\Builder;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Service extends AbstractService
{
    public static function factory(Builder $builder, $baseUrl, array $options = [])
    {
        $httpClient = $builder->httpClient($baseUrl, $options);

        return new self($httpClient, new Api());
    }

    public function generateToken(array $options)
    {
        return $this->model('Token')->create($options);
    }
}