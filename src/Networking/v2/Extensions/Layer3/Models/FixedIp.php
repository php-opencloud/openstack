<?php

namespace OpenStack\Networking\v2\Extensions\Layer3\Models;

use OpenCloud\Common\Resource\AbstractResource;

class FixedIp extends AbstractResource
{
    /** @var string */
    public $subnetId;

    /** @var string */
    public $ip;

    protected $aliases = [
        'subnet_id' => 'subnetId'
    ];
}
