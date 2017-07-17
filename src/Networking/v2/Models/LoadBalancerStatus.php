<?php declare(strict_types=1);

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Transport\Utils;

/**
 * Represents Neutron v2 LoadBalancer Stats
 *
 * @property Api $api
 */
class LoadBalancerStatus extends OperatorResource implements Retrievable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $loadbalancerId;

    /**
     * @var string
     */
    public $operatingStatus;

    /**
     * @var string
     */
    public $provisioningStatus;

    /**
     * @var LoadBalancerListener[]
     */
    public $listeners;

    /**
     * @var LoadBalancer
     */
    public $loadbalancer;

    protected $resourceKey = 'statuses';

    protected $aliases = [
        'loadbalancer_id' => 'loadbalancerId'
    ];

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getLoadBalancerStatuses(), ['loadbalancerId' => (string)$this->loadbalancerId]);
        $this->populateFromResponse($response);
        $this->flattenStatus();
    }

    /**
     * Flatten this class to something more usable
     */
    private function flattenStatus()
    {
        if ($this->loadbalancer instanceof LoadBalancer) {
            $this->name = $this->loadbalancer->name;
            $this->id = $this->loadbalancer->id;
            $this->operatingStatus = $this->loadbalancer->operatingStatus;
            $this->provisioningStatus = $this->loadbalancer->provisioningStatus;
            $this->listeners = $this->loadbalancer->listeners;
            unset($this->loadbalancer);
        }
    }
}
