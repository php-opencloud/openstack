<?php

namespace OpenStack\Networking\v2\Extensions\Layer3\Models;

use OpenCloud\Common\Resource\OperatorResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Retrievable;
use OpenCloud\Common\Resource\Updateable;
use OpenStack\Networking\v2\Extensions\Layer3\Api;

/**
 * @property Api $api
 */
class FloatingIp extends OperatorResource implements Listable, Creatable, Retrievable, Updateable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $status;

    /** @var string */
    public $floatingNetworkId;

    /** @var string */
    public $routerId;

    /** @var string */
    public $fixedIpAddress;

    /** @var string */
    public $floatingIpAddress;

    /** @var string */
    public $tenantId;

    /** @var string */
    public $portId;

    protected $aliases = [
        "floating_network_id" => 'floatingNetworkId',
        "router_id"           => 'routerId',
        "fixed_ip_address"    => 'fixedIpAddress',
        "floating_ip_address" => 'floatingIpAddress',
        "tenant_id"           => 'tenantId',
        "port_id"             => 'portId',
    ];

    protected $resourceKey = 'floatingip';
    protected $resourcesKey = 'floatingips';

    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postFloatingIps(), $userOptions);
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $response = $this->executeWithState($this->api->putFloatingIp());
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteFloatingIp());
    }

    public function retrieve()
    {
        $this->executeWithState($this->api->getFloatingIp());
    }
}