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
        $this->populateFromResponse($response);
        return $this;
    }

    public function retrieve()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function listUserGroups()
    {

    }

    public function listUserProjects()
    {

    }
}