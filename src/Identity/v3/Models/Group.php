<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Error\BadResponseError;
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
    protected $resourcesKey = 'groups';

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postGroups}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postGroups(), $data);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getGroup(), ['id' => $this->id]);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->patchGroup());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteGroup(), ['id' => $this->id]);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::getGroupUsers}
     *
     * @return \Generator
     */
    public function listUsers(array $options = [])
    {
        $options['id'] = $this->id;
        return $this->model(User::class)->enumerate($this->api->getGroupUsers(), $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::putGroupUser}
     */
    public function addUser(array $options)
    {
        $this->execute($this->api->putGroupUser(), ['groupId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::deleteGroupUser}
     */
    public function removeUser(array $options)
    {
        $this->execute($this->api->deleteGroupUser(), ['groupId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::headGroupUser}
     *
     * @return bool
     */
    public function checkMembership(array $options)
    {
        try {
            $this->execute($this->api->headGroupUser(), ['groupId' => $this->id] + $options);
            return true;
        } catch (BadResponseError $e) {
            return false;
        }
    }
}
