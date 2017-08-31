<?php declare(strict_types=1);

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\HasWaiterTrait;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;

/**
 * Represents a Networking v2 Network.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class NetworkIpAvailability extends OperatorResource implements Listable, Retrievable
{
    use HasWaiterTrait;

    /** @var string */
    public $networkId;

    /** @var string */
    public $networkName;

    /** @var string */
    public $tenantId;

    /** @var string */
    public $projectId;

    /** @var int */
    public $totalIps;

    /** @var int */
    public $usedIps;

    /** @var array */
    public $subnetIpAvailability;

    protected $aliases = [
        'network_id'             => 'networkId',
        'network_name'           => 'networkName',
        'tenant_id'              => 'tenantId',
        'project_id'             => 'projectId',
        'total_ips'              => 'totalIps',
        'used_ips'               => 'usedIps',
        'subnet_ip_availability' => 'subnetIpAvailability'
    ];

    protected $resourceKey = 'network_ip_availability';
    protected $resourcesKey = 'network_ip_availabilities';
    protected $markerKey = 'network_id';

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getNetworkIpAvailability(), ['newtork_id' => (string)$this->networkId]);
        $this->populateFromResponse($response);
    }
}
