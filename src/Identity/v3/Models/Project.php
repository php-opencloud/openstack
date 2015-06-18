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
class Project extends AbstractResource implements Creatable, Retrievable, Listable, Updateable, Deletable
{
    /** @var string */
    public $domainId;

    /** @var string */
    public $parentId;

    /** @var bool */
    public $enabled;

    /** @var string */
    public $id;

    /** @var array */
    public $links;

    /** @var string */
    public $name;

    protected $aliases = [
        'domain_id' => 'domainId',
        'parent_id' => 'parentId',
    ];

    protected $resourceKey = 'project';

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->domain = $this->model('Domain', $data);
    }

    public function create(array $data)
    {
        $response = $this->execute($this->api->postProjects(), $data);
        $this->populateFromResponse($response);
        return $this;
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getProject());
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $response = $this->executeWithState($this->api->patchProject());
        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteProject());
    }

    public function listUserRoles(array $options)
    {
        $operation = $this->getOperation($this->api->getProjectUserRoles(), ['projectId' => $this->id] + $options);
        return $this->model('Role')->enumerate($operation);
    }

    public function grantUserRole(array $options)
    {
        $this->execute($this->api->putProjectUserRole(), ['projectId' => $this->id] + $options);
    }

    public function checkUserRole(array $options)
    {
        $response = $this->execute($this->api->headProjectUserRole(), ['projectId' => $this->id] + $options);
        return $response->getStatusCode() === 200;
    }

    public function revokeUserRole(array $options)
    {
        $this->execute($this->api->deleteProjectUserRole(), ['projectId' => $this->id] + $options);
    }

    public function listGroupRoles(array $options)
    {
        $operation = $this->getOperation($this->api->getProjectGroupRoles(), ['projectId' => $this->id] + $options);
        return $this->model('Role')->enumerate($operation);
    }

    public function grantGroupRole(array $options)
    {
        $this->execute($this->api->putProjectGroupRole(), ['projectId' => $this->id] + $options);
    }

    public function checkGroupRole(array $options)
    {
        $response = $this->execute($this->api->headProjectGroupRole(), ['projectId' => $this->id] + $options);
        return $response->getStatusCode() === 200;
    }

    public function revokeGroupRole(array $options)
    {
        $this->execute($this->api->deleteProjectGroupRole(), ['projectId' => $this->id] + $options);
    }
}