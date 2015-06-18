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
class User extends AbstractResource implements Creatable, Listable, Retrievable, Updateable, Deletable
{
    /** @var Domain */
    public $domain;

    /** @var string */
    public $defaultProjectId;

    /** @var string */
    public $id;

    /** @var string */
    public $email;

    /** @var bool */
    public $enabled;

    /** @var string */
    public $description;

    /** @var array */
    public $links;

    /** @var string */
    public $name;

    protected $aliases = [
        'domain_id' => 'domainId',
        'default_project_id' => 'defaultProjectId'
    ];

    protected $resourceKey = 'user';

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        if (isset($data['domainId']) || isset($data['domain_id'])) {
            $domainId = isset($data['domain_id']) ? $data['domain_id'] : $data['domainId'];
            $this->domain = $this->model('Domain', ['id' => $domainId]);
        }
    }

    public function create(array $data)
    {
        $response = $this->execute($this->api->postUsers(), $data);
        return $this->populateFromResponse($response);
    }

    public function retrieve()
    {
        $response = $this->execute($this->api->getUser(), ['id' => $this->id]);
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $attrs = ['id', 'defaultProjectId', 'description', 'email', 'enabled'];
        $response = $this->execute($this->api->patchUser(), $this->getAttrs($attrs));
        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteUser(), ['id' => $this->id]);
    }

    public function listGroups()
    {
        $operation = $this->getOperation($this->api->getUserGroups(), ['id' => $this->id]);
        return $this->model('Group')->enumerate($operation);
    }

    public function listProjects()
    {
        $operation = $this->getOperation($this->api->getUserProjects(), ['id' => $this->id]);
        return $this->model('Project')->enumerate($operation);
    }
}