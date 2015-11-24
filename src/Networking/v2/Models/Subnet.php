<?php

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

/**
 * Represents a Networking v2 Network.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class Subnet extends AbstractResource implements Listable, Retrievable, Creatable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var bool */
    public $enableDhcp;

    /** @var string */
    public $networkId;

    /** @var array */
    public $dnsNameservers;

    /** @var array */
    public $allocationPools;

    /** @var array */
    public $hostRoutes;

    /** @var int */
    public $ipVersion;

    /** @var string */
    public $gatewayIp;

    /** @var string */
    public $cidr;

    /** @var string */
    public $tenantId;

    /** @var array */
    public $links;

    protected $aliases = [
        'enable_dhcp' => 'enableDhcp',
        'network_id' => 'networkId',
        'dns_nameservers' => 'dnsNameservers',
        'allocation_pools' => 'allocationPools',
        'host_routes' => 'hostRoutes',
        'ip_version' => 'ipVersion',
        'gateway_ip' => 'gatewayIp',
        'tenant_id' => 'tenantId'
    ];

    protected $resourceKey = 'subnet';
    protected $resourcesKey = 'subnets';

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getSubnet(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }

    /**
     * Creates multiple subnets in a single request.
     *
     * @param array $data {@see \OpenStack\Networking\v2\Api::postSubnets}
     * @return Subnet[]
     */
    public function bulkCreate(array $data)
    {
        $response = $this->execute($this->api->postSubnets(), ['subnets' => $data]);
        $subnetsData = Utils::jsonDecode($response)['subnets'];

        $subnets = [];
        foreach ($subnetsData as $resourceData) {
            $resource = $this->newInstance();
            $resource->populateFromArray($resourceData);
            $subnets[] = $resource;
        }

        return $subnets;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Networking\v2\Api::postSubnet}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postSubnet(), $data);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->putSubnet());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteSubnet());
    }
}
