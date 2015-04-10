<?php

namespace OpenStack\Identity\v2\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\ValueResource;

class Token extends AbstractResource
{
    public $issuedAt;
    public $id;
    public $expires;
    public $tenant;

    public function populateFromResponse(ResponseInterface $response)
    {
        $this->populateFromArray($response->json()['access']['token']);

        return $this;
    }

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->issuedAt = new \DateTimeImmutable($data['issued_at']);
        $this->expires  = new \DateTimeImmutable($data['expires'], $this->issuedAt->getTimezone());

        if (isset($data['tenant'])) {
            $this->tenant = $this->model('Tenant', $data['tenant']);
        }
    }

    public function hasExpired()
    {
        return $this->expires <= new \DateTimeImmutable('now', $this->expires->getTimezone());
    }
}