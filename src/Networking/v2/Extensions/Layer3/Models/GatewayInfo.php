<?php

namespace OpenStack\Networking\v2\Extensions\Layer3\Models;

use OpenStack\Common\Resource\AbstractResource;

class GatewayInfo extends AbstractResource
{
    /** @var string */
    public $networkId;

    /** @var string */
    public $enableSnat;

    /** @var []FixedIp */
    public $fixedIps;

    protected $aliases = [
        'network_id'  => 'networkId',
        'enable_snat' => 'enableSnat',
        'fixed_ips'   => 'fixedIps',
    ];
}
