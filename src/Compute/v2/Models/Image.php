<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;

class Image extends AbstractResource
{
    public $created;
    public $id;
    public $links;
    public $metadata;
    public $minDisk;
    public $minRam;
    public $name;
    public $progress;
    public $status;
    public $updated;

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->created = new \DateTimeImmutable($this->created);
        $this->updated = new \DateTimeImmutable($this->updated);
    }
}