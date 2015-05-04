<?php

namespace OpenStack\Identity\v3\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsCreatable;

class Token extends AbstractResource implements IsCreatable
{
    public $methods;
    public $roles;
    public $expires;
    public $project;
    public $catalog;
    public $extras;
    public $user;
    public $issuedAt;
    public $id;

    protected $resourceKey = 'token';

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->id = $response->getHeader('X-Subject-Token');
    }

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->issuedAt = new \DateTimeImmutable($data['issued_at']);
        $this->expires  = new \DateTimeImmutable($data['expires_at']);

        foreach ($data['roles'] as $roleData) {
            $this->roles[] = $this->model('Role', $roleData);
        }

        $this->project = $this->model('Project', $data['project']);
        $this->catalog = $this->model('Catalog', $data['catalog']);
        $this->user = $this->model('User', $data['user']);
    }

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
            $data['methods'] = ['tokens'];
        } else {
            throw new \RuntimeException('Either a user or token must be provided.');
        }

        $response = $this->execute($this->api->postTokens(), $data);

        return $this->populateFromResponse($response);
    }
}