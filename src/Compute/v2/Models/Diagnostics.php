<?php

declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\HasWaiterTrait;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */

class Diagnostics extends OperatorResource implements Retrievable
{
    use HasWaiterTrait;

    /** @var string */
    public $id;

    /** @var string */
    public $config_drive;

    /** @var array */
    public $cpu_details = [
        'id' => null,
        'time' => null,
        'utilisation' => null,
    ];

    /** @var array */
    public $disk_details = [
        'errors_count' => null,
        'read_bytes' => null,
        'read_requests' => null,
        'write_bytes' => null,
        'write_requests' => null,
    ];

    /** @var string */
    public $driver = null;

    /** @var string */
    public $hypervisor = null;

    /** @var string */
    public $hypervisor_os = null;

    /** @var array */
    public $memory_details = [
        'maximum' => null,
        'used' => null,
    ];

    /** @var array */
    public $nic_details = [
        'mac_address' => null,
        'rx_octets' => null,
        'rx_drop' => null,
        'rx_errors' => null,
        'rx_packets' => null,
        'rx_rate' => null,
        'tx_octets' => null,
        'tx_drop' => null,
        'tx_errors' => null,
        'tx_packets' => null,
        'tx_rate' => null,
    ];

    /** @var string */
    public $num_cpus = null;

    /** @var string */
    public $num_disks = null;

    /** @var string */
    public $num_nics = null;

    /** @var string */
    public $state = null;

    /** @var string */
    public $uptime = null;

    /** @var string */
    public $memory = null;

    /** @var string */
    public $memoryUsable = null;

    protected $aliases = [
        'memory-usable' => 'memoryUsable'
    ];

    protected $resourceKey  = 'server';
    protected $resourcesKey = 'servers';
    protected $markerKey    = 'id';

    public function retrieve()
    {
        $response = $this->execute($this->api->getServerDiagnostics(), ['serverId' => (string) $this->getAttrs(['id'])]);
        return $this->populateFromResponse($response);
    }
}
