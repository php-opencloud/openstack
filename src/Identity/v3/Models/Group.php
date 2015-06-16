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
class Group extends AbstractResource implements IsCreatable, IsListable, IsRetrievable, IsUpdateable, IsDeletable
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

    public function listUsers()
    {

    }

    public function createUser()
    {

    }

    public function deleteUser()
    {

    }

    public function checkUserMembership()
    {

    }
}