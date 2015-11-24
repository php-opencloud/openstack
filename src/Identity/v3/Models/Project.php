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
class Project extends AbstractResource implements Creatable, Retrievable, Listable, Updateable, Deletable
{
    /** @var string */
    public $domainId;

    /** @var string */
    public $parentId;

    /** @var bool */
    public $enabled;

    /** @var string */
    public $description;

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
    protected $resourcesKey = 'projects';

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postProjects}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postProjects(), $data);
        $this->populateFromResponse($response);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getProject());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->patchProject());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteProject());
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::getProjectUserRoles}
     *
     * @return \Generator
     */
    public function listUserRoles(array $options = [])
    {
        $options['projectId'] = $this->id;
        return $this->model(Role::class)->enumerate($this->api->getProjectUserRoles(), $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::putProjectUserRole}
     */
    public function grantUserRole(array $options)
    {
        $this->execute($this->api->putProjectUserRole(), ['projectId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::headProjectUserRole}
     *
     * @return bool
     */
    public function checkUserRole(array $options)
    {
        try {
            $this->execute($this->api->headProjectUserRole(), ['projectId' => $this->id] + $options);
            return true;
        } catch (BadResponseError $e) {
            return false;
        }
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::deleteProjectUserRole}
     */
    public function revokeUserRole(array $options)
    {
        $this->execute($this->api->deleteProjectUserRole(), ['projectId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::getProjectGroupRoles}
     *
     * @return \Generator
     */
    public function listGroupRoles(array $options = [])
    {
        $options['projectId'] = $this->id;
        return $this->model(Role::class)->enumerate($this->api->getProjectGroupRoles(), $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::putProjectGroupRole}
     */
    public function grantGroupRole(array $options)
    {
        $this->execute($this->api->putProjectGroupRole(), ['projectId' => $this->id] + $options);
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::headProjectGroupRole}
     *
     * @return bool
     */
    public function checkGroupRole(array $options)
    {
        try {
            $this->execute($this->api->headProjectGroupRole(), ['projectId' => $this->id] + $options);
            return true;
        } catch (BadResponseError $e) {
            return false;
        }
    }

    /**
     * @param array $options {@see \OpenStack\Identity\v3\Api::deleteProjectGroupRole}
     */
    public function revokeGroupRole(array $options)
    {
        $this->execute($this->api->deleteProjectGroupRole(), ['projectId' => $this->id] + $options);
    }
}
