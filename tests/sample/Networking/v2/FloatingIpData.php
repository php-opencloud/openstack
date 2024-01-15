<?php

namespace OpenStack\Sample\Networking\v2;

use OpenStack\Networking\v2\Service;

class FloatingIpData
{
    /** @var \OpenStack\Networking\v2\Models\Network */
    public $externalNetwork;

    /** @var \OpenStack\Networking\v2\Models\Subnet */
    public $externalSubnet;

    /** @var \OpenStack\Networking\v2\Models\Network */
    public $internalNetwork;

    /** @var \OpenStack\Networking\v2\Models\Subnet */
    public $internalSubnet;

    /** @var \OpenStack\Networking\v2\Models\Port */
    public $port;

    /** @var \OpenStack\Networking\v2\Extensions\Layer3\Models\Router */
    public $router;

    /** @var \OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp */
    public $floatingIp;
}