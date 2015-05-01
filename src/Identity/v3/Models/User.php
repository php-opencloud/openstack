<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;

class User extends AbstractResource
{
    public $domain;
    public $id;
    public $name;
    public $links;

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->domain = $this->model('Domain', $data);
    }
} 