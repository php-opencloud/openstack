<?php

declare(strict_types=1);

namespace OpenStack\Identity\v2;

use App\Service\Rackspace\Params;
use OpenStack\Common\Api\ApiInterface;

/**
 * Represents the OpenStack Identity v2 API.
 */
class Api implements ApiInterface
{
    protected $params;

    public function __construct()
    {
        $this->params = new Params();
    }

    public function postToken(): array
    {
        return [
            'method' => 'POST',
            'path'   => 'tokens',
            'params' => [
                'username' => $this->params->username(),
                'apiKey'   => $this->params->apiKey(),
            ],
        ];
    }
}
