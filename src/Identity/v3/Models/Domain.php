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
        $response = $this->execute($this->api->getDomain(), $this->getAttrs(['id']));
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $def = $this->api->patchDomain();
        $response = $this->execute($def, $this->getAttrs(array_keys($def['params'])));
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteDomain(), $this->getAttrs(['id']));
    }

    public function listUserRoles(array $options = [])
    {
        $operation = $this->getOperation($this->api->getUserRoles(), ['domainId' => $this->id] + $options);
        return $this->model('Role')->enumerate($operation);
    }

    public function grantUserRole(array $options = [])
    {
        $this->execute($this->api->putUserRoles(), ['domainId' => $this->id] + $options);
    }

    public function checkUserRole(array $options = [])
    {
        $response = $this->execute($this->api->headUserRole(), ['domainId' => $this->id] + $options);
        return $response->getStatusCode() === 200;
    }

    public function revokeUserRole(array $options = [])
    {
        $this->execute($this->api->deleteUserRole(), ['domainId' => $this->id] + $options);
    }

    public function listGroupRoles(array $options = [])
    {
        $operation = $this->getOperation($this->api->getGroupRoles(), ['domainId' => $this->id] + $options);
        return $this->model('Role')->enumerate($operation);
    }

    public function grantGroupRole(array $options = [])
    {
        $this->execute($this->api->putGroupRole(), ['domainId' => $this->id] + $options);
    }

    public function checkGroupRole(array $options = [])
    {
        $response = $this->execute($this->api->headGroupRole(), ['domainId' => $this->id] + $options);
        return $response->getStatusCode() === 200;
    }

    public function revokeGroupRole(array $options = [])
    {
        $this->execute($this->api->deleteGroupRole(), ['domainId' => $this->id] + $options);
    }
} 