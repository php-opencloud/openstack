<?php

namespace OpenStack\Identity\v3\Models;

use Psr\Http\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Retrievable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Token extends AbstractResource implements Creatable, Retrievable, \OpenStack\Common\Auth\Token
{
    /** @var array */
    public $methods;

    /** @var []Role */
    public $roles;

    /** @var \DateTimeImmutable */
    public $expires;

    /** @var Project */
    public $project;

    /** @var Catalog */
    public $catalog;

    /** @var mixed */
    public $extras;

    /** @var User */
    public $user;

    /** @var \DateTimeImmutable */
    public $issued;

    /** @var string */
    public $id;

    protected $resourceKey = 'token';
    protected $resourcesKey = 'tokens';

    protected $aliases = [
        'expires_at' => 'expires',
        'issued_at'  => 'issued'
    ];

    /**
     * {@inheritDoc}
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->id = $response->getHeaderLine('X-Subject-Token');

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool TRUE if the token has expired (and is invalid); FALSE otherwise.
     */
    public function hasExpired()
    {
        return $this->expires <= new \DateTimeImmutable('now', $this->expires->getTimezone());
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getTokens(), ['tokenId' => $this->id]);

        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postTokens}
     */
    public function create(array $data)
    {
        if (isset($data['user'])) {
            $data['methods'] = ['password'];
            if (!isset($data['user']['id']) && empty($data['user']['domain'])) {
                throw new \InvalidArgumentException(
                    'When authenticating with a username, you must also provide either the domain name or domain ID to '
                    . 'which the user belongs to. Alternatively, if you provide a user ID instead, you do not need to '
                    . 'provide domain information.'
                );
            }
        } elseif (isset($data['tokenId'])) {
            $data['methods'] = ['token'];
        } else {
            throw new \InvalidArgumentException('Either a user or token must be provided.');
        }

        $response = $this->execute($this->api->postTokens(), $data);
        return $this->populateFromResponse($response);
    }
}
