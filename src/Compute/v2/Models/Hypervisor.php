<?php declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Hypervisor extends OperatorResource implements
    Retrievable,
    Listable
{
    /** @var int */
    public $id;

    /** @var string */
    public $status;

    /** @var string */
    public $state;

    /** @var string */
    public $host_ip;

    /** @var int */
    public $free_disk_gb;

    /** @var int */
    public $free_ram_mb;

    /** @var string */
    public $hypervisor_hostname;

    /** @var string */
    public $hypervisor_type;

    /** @var string */
    public $hypervisor_version;

    /** @var int */
    public $local_gb;

    /** @var int */
    public $local_gb_used;

    /** @var int */
    public $memory_mb;

    /** @var int */
    public $memory_mb_used;

    /** @var int */
    public $running_vms;

    /** @var int */
    public $vcpus;

    /** @var int */
    public $vcpus_used;

    /** @var array */
    public $service;

    protected $resourceKey = 'hypervisor';
    protected $resourcesKey = 'hypervisors';

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getHypervisor(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }
}
