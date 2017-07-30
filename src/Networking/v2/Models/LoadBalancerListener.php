<?php declare(strict_types=1);

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;
use OpenStack\Networking\v2\Api;

/**
 * Represents a Neutron v2 LoadBalancer Listener
 *
 * @property Api $api
 */
class LoadBalancerListener extends OperatorResource implements Creatable, Retrievable, Updateable, Deletable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $tenantId;

    /**
     * @var string
     */
    public $protocol;

    /**
     * @var integer
     */
    public $protocolPort;

    /**
     * @var integer
     */
    public $connectionLimit;

    /**
     * @var string
     */
    public $defaultPoolId;

    /**
     * @var boolean
     */
    public $adminStateUp;

    /**
     * @var LoadBalancer[]
     */
    public $loadbalancers;

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
     * @var LoadBalancerPool[]
     */
    public $pools;

    protected $resourcesKey = 'listeners';
    protected $resourceKey = 'listener';

    protected $aliases = [
        'tenant_id'           => 'tenantId',
        'admin_state_up'      => 'adminStateUp',
        'protocol_port'       => 'protocolPort',
        'connection_limit'    => 'connectionLimit',
        'default_pool_id'     => 'defaultPoolId',
        'loadbalancer_id'     => 'loadbalancerId',
        'operating_status'    => 'operatingStatus',
        'provisioning_status' => 'provisioningStatus'
    ];

    /**
     * {@inheritDoc}
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postLoadBalancerListener(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getLoadBalancerListener(), ['id' => (string)$this->id]);
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->putLoadBalancerListener());
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteLoadBalancerListener());
    }
}
