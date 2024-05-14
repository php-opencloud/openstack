<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params();
    }

    public function getDnsZones(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'v2/dns/', // Последний слэш это ньанс от VK
            'params' => [
                'limit'  => $this->params->limit(),
                'marker' => $this->params->marker(),
                'tenant' => $this->params->tenant(),
            ],
        ];
    }

    public function getDnsZone(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'v2/dns/{uuid}',
            'params' => [
                'limit'  => $this->params->limit(),
                'marker' => $this->params->marker(),
                'uuid'   => $this->params->uuid(),
            ],
        ];
    }
}
