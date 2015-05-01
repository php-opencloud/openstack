<?php

namespace OpenStack\Identity\v2\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\ValueResource;

/**
 * Represents an Identity v2 Token.
 *
 * @package OpenStack\Identity\v2\Models
 */
class Token extends AbstractResource
{
    /** @var \DatetimeImmutable */
    public $issuedAt;

    /** @var string */
    public $id;

    /** @var \DatetimeImmutable */
    public $expires;

    /** @var Tenant */
    public $tenant;

    /**
     * {@inheritDoc}
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        $this->populateFromArray($response->json()['access']['token']);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->issuedAt = new \DateTimeImmutable($data['issued_at']);
        $this->expires  = new \DateTimeImmutable($data['expires'], $this->issuedAt->getTimezone());

        if (isset($data['tenant'])) {
            $this->tenant = $this->model('Tenant', $data['tenant']);
        }
    }

    /**
     * Indicates whether the token has expired or not.
     *
     * @return bool TRUE if the token has expired, FALSE if it is still valid
     */
    public function hasExpired()
    {
        return $this->expires <= new \DateTimeImmutable('now', $this->expires->getTimezone());
    }
}