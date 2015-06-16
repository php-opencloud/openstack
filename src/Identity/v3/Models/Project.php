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

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function listUserRoles()
    {

    }

    public function grantUserRole()
    {

    }

    public function checkUserRole()
    {

    }

    public function revokeUserRole()
    {

    }

    public function listGroupRoles()
    {

    }

    public function grantGroupRole()
    {

    }

    public function checkGroupRole()
    {

    }

    public function revokeGroupRole()
    {

    }
}