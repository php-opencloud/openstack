<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsCreatable;
use OpenStack\Common\Resource\IsDeletable;
use OpenStack\Common\Resource\IsListable;
use OpenStack\Common\Resource\IsRetrievable;
use OpenStack\Common\Resource\IsUpdateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Project extends AbstractResource implements IsCreatable, IsRetrievable, IsListable, IsUpdateable, IsDeletable
{
    /** @var string */
    public $domainId;

    /** @var string */
    public $id;

    /** @var array */
    public $links;

    /** @var string */
    public $name;

    protected $aliases = ['domain_id' => 'domainId'];

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->domain = $this->model('Domain', $data);
    }

    public function create(array $data)
    {

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