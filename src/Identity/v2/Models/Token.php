<?php

namespace OpenStack\Identity\v2\Models;

use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\ValueResource;

/**
 * Represents an Identity v2 Token.
 *
 * @package OpenStack\Identity\v2\Models
 */
class Token extends AbstractResource implements \OpenStack\Common\Auth\Token
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
        $this->populateFromArray(Utils::jsonDecode($response)['access']['token']);

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

    public function getId()
    {
        return $this->id;
    }

    public function hasExpired()
    {
        return $this->expires <= new \DateTimeImmutable('now', $this->expires->getTimezone());
    }
}