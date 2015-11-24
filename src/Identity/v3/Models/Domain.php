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
class Domain extends AbstractResource implements Creatable, Listable, Retrievable, Updateable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var array */
    public $links;

    /** @var bool */
    public $enabled;

    /** @var string */
    public $description;

    protected $resourceKey = 'domain';
    protected $resourcesKey = 'domains';

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postDomains}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postDomains(), $data);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getDomain());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->patchDomain());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteDomain());
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::getUserRoles}
     *
     * @return \Generator
     */
    public function listUserRoles(array $options = [])
    {
        $options['domainId'] = $this->id;
        return $this->model(Role::class)->enumerate($this->api->getUserRoles(), $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::putUserRoles}
     */
    public function grantUserRole(array $options = [])
    {
        $this->execute($this->api->putUserRoles(), ['domainId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::headUserRole}
     *
     * @return bool
     */
    public function checkUserRole(array $options = [])
    {
        try {
            $this->execute($this->api->headUserRole(), ['domainId' => $this->id] + $options);
            return true;
        } catch (BadResponseError $e) {
            return false;
        }
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::deleteUserRole}
     */
    public function revokeUserRole(array $options = [])
    {
        $this->execute($this->api->deleteUserRole(), ['domainId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::getGroupRoles}
     *
     * @return \Generator
     */
    public function listGroupRoles(array $options = [])
    {
        $options['domainId'] = $this->id;
        return $this->model(Role::class)->enumerate($this->api->getGroupRoles(), $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::putGroupRole}
     */
    public function grantGroupRole(array $options = [])
    {
        $this->execute($this->api->putGroupRole(), ['domainId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::headGroupRole}
     *
     * @return bool
     */
    public function checkGroupRole(array $options = [])
    {
        try {
            $this->execute($this->api->headGroupRole(), ['domainId' => $this->id] + $options);
            return true;
        } catch (BadResponseError $e) {
            return false;
        }
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::deleteGroupRole}
     */
    public function revokeGroupRole(array $options = [])
    {
        $this->execute($this->api->deleteGroupRole(), ['domainId' => $this->id] + $options);
    }
}
