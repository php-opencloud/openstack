<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;

class Flavor extends AbstractResource
{
    public $disk;
    public $id;
    public $name;
    public $ram;
    public $vcpus;
    public $links;
}