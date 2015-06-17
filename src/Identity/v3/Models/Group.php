<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Group extends AbstractResource implements Creatable, Listable, Retrievable, Updateable, Deletable
{
    /** @var string */
    public $domainId;

    /** @var string */
    public $id;

    /** @var string */
    public $description;

    /** @var array */
    public $links;

    /** @var string */
    public $name;

    protected $aliases = ['domain_id' => 'domainId'];

    protected $resourceKey = 'group';

    public function create(array $data)
    {
        $response = $this->execute($this->api->postGroups(), $data);
        return $this->populateFromResponse($response);
    }

    public function retrieve()
    {
        $response = $this->execute($this->api->getGroup(), ['id' => $this->id]);
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $response = $this->executeWithState($this->api->patchGroup());
        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteGroup(), ['id' => $this->id]);
    }

    public function listUsers(array $options = [])
    {
        $operation = $this->getOperation($this->api->getGroupUsers(), ['id' => $this->id] + $options);
        return $this->model('User')->enumerate($operation);
    }

    public function addUser(array $options)
    {
        $this->execute($this->api->putGroupUser(), ['groupId' => $this->id] + $options);
    }

    public function removeUser(array $options)
    {
        $this->execute($this->api->deleteGroupUser(), ['groupId' => $this->id] + $options);
    }

    public function checkMembership(array $options)
    {
        $response = $this->execute($this->api->headGroupUser(), ['groupId' => $this->id] + $options);
        return $response->getStatusCode() === 200;
    }
}