<?php

namespace OpenStack\Identity\v2\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\ValueResource;

class Token extends ValueResource
{
    public $issuedAt;
    public $id;
    public $expires;
    public $tenant;

    public function fromResponse(ResponseInterface $response)
    {
        $data = $response->json()['access']['token'];

        $this->issuedAt = new \DateTimeImmutable($data['issued_at']);
        $this->expires  = new \DateTimeImmutable($data['expires'], $this->issuedAt->getTimezone());
        $this->id       = $data['id'];
        $this->tenant   = new Tenant($data['tenant']);
    }

    public function hasExpired()
    {
        return $this->expires <= new \DateTimeImmutable('now', $this->expires->getTimezone());
    }
}