<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Listable;

class Assignment extends AbstractResource implements Listable
{
    /** @var Role */
    public $role;

    /** @var \stdClass */
    public $scope;

    /** @var Group */
    public $group;

    /** @var User */
    public $user;

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        if (isset($data['role'])) {
            $this->role = $this->model('Role', $data['role']);
        }

        if (isset($data['group'])) {
            $this->group = $this->model('Group', $data['group']);
        }

        if (isset($data['user'])) {
            $this->user = $this->model('User', $data['user']);
        }
    }
}