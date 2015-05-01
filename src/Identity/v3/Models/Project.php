<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;

class Project extends AbstractResource
{
    public $domain;
    public $id;
    public $links;
    public $name;

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->domain = $this->model('Domain', $data);
    }
}