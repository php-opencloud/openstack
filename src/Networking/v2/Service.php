<?php

declare(strict_types=1);

namespace OpenStack\Networking\v2;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Networking\v2\Models\LoadBalancer;
use OpenStack\Networking\v2\Models\LoadBalancerHealthMonitor;
use OpenStack\Networking\v2\Models\LoadBalancerListener;
use OpenStack\Networking\v2\Models\LoadBalancerMember;
use OpenStack\Networking\v2\Models\LoadBalancerPool;
use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Models\Pool;
use OpenStack\Networking\v2\Models\Port;
use OpenStack\Networking\v2\Models\Quota;
use OpenStack\Networking\v2\Models\Subnet;

/**
 * Network v2 service for OpenStack.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * Create a new network resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postNetwork}
     *
     * @return Network
     */
    public function createNetwork(array $options): Network
    {
        return $this->model(Network::class)->create($options);
    }

    /**
     * Create a new network resources.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postNetworks}
     *
     * @return array
     */
    public function createNetworks(array $options): array
    {
        return $this->model(Network::class)->bulkCreate($options);
    }

    /**
     * Retrieve a network object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Network::retrieve}.
     *
     * @param string $id
     *
     * @return Network
     */
    public function getNetwork(string $id): Network
    {
        return $this->model(Network::class, ['id' => $id]);
    }

    /**
     * List networks.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::getNetworks}
     *
     * @return \Generator
     */
    public function listNetworks(array $options = []): \Generator
    {
        return $this->model(Network::class)->enumerate($this->api->getNetworks(), $options);
    }

    /**
     * Create a new subnet resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postSubnet}
     *
     * @return Subnet
     */
    public function createSubnet(array $options): Subnet
    {
        return $this->model(Subnet::class)->create($options);
    }

    /**
     * Create a new subnet resources.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postSubnets}
     *
     * @return []Subnet
     */
    public function createSubnets(array $options): array
    {
        return $this->model(Subnet::class)->bulkCreate($options);
    }

    /**
     * Retrieve a subnet object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Subnet::retrieve}.
     *
     * @param string $id
     *
     * @return Subnet
     */
    public function getSubnet(string $id): Subnet
    {
        return $this->model(Subnet::class, ['id' => $id]);
    }

    /**
     * List subnets.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::getSubnets}
     *
     * @return \Generator
     */
    public function listSubnets(array $options = []): \Generator
    {
        return $this->model(Subnet::class)->enumerate($this->api->getSubnets(), $options);
    }

    /**
     * Create a new port resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postSinglePort}
     *
     * @return Port
     */
    public function createPort(array $options): Port
    {
        return $this->model(Port::class)->create($options);
    }

    /**
     * Create new port resources.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postMultiplePorts}
     *
     * @return []Port
     */
    public function createPorts(array $options): array
    {
        return $this->model(Port::class)->bulkCreate($options);
    }

    /**
     * Retrieve a subnet object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Port::retrieve}.
     *
     * @param string $id
     *
     * @return Port
     */
    public function getPort(string $id): Port
    {
        return $this->model(Port::class, ['id' => $id]);
    }

    /**
     * List ports.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::getPorts}
     *
     * @return \Generator
     */
    public function listPorts(array $options = []): \Generator
    {
        return $this->model(Port::class)->enumerate($this->api->getPorts(), $options);
    }

    /**
     * Lists quotas for projects with non-default quota values.
     *
     * @return \Generator
     */
    public function listQuotas(): \Generator
    {
        return $this->model(Quota::class)->enumerate($this->api->getQuotas(), []);
    }

    /**
     * Lists quotas for a project.
     *
     * Retrieve a quota object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see Quota::retrieve}.
     *
     * @param string $tenantId
     *
     * @return Quota
     */
    public function getQuota(string $tenantId): Quota
    {
        return $this->model(Quota::class, ['tenantId' => $tenantId]);
    }

    /**
     * Lists default quotas for a project.
     *
     * @param string $tenantId
     *
     * @return Quota
     */
    public function getDefaultQuota(string $tenantId): Quota
    {
        $quota = $this->model(Quota::class, ['tenantId' => $tenantId]);
        $quota->populateFromResponse($this->execute($this->api->getQuotaDefault(), ['tenantId' => $tenantId]));

        return $quota;
    }

    /**
     * Lists loadbalancers for projects.
     *
     * @return \Generator
     */
    public function listLoadBalancers(): \Generator
    {
        return $this->model(LoadBalancer::class)->enumerate($this->api->getLoadBalancers());
    }

    /**
     * Retrieve an instance of a LoadBalancer object.
     *
     * @param string $id
     *
     * @return LoadBalancer
     */
    public function getLoadBalancer(string $id): LoadBalancer
    {
        return $this->model(LoadBalancer::class, ['id' => $id]);
    }

    /**
     * Create a new loadbalancer resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postLoadBalancer}
     *
     * @return LoadBalancer
     */
    public function createLoadBalancer(array $options): LoadBalancer
    {
        return $this->model(LoadBalancer::class)->create($options);
    }

    /**
     * Lists loadbalancer listeners.
     *
     * @return \Generator
     */
    public function listLoadBalancerListeners(): \Generator
    {
        return $this->model(LoadBalancerListener::class)->enumerate($this->api->getLoadBalancerListeners());
    }

    /**
     * Retrieve an instance of a loadbalancer listener object.
     *
     * @param string $id
     *
     * @return LoadBalancerListener
     */
    public function getLoadBalancerListener(string $id): LoadBalancerListener
    {
        return $this->model(LoadBalancerListener::class, ['id' => $id]);
    }

    /**
     * Create a new loadbalancer Listener resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postLoadBalancerListener}
     *
     * @return LoadBalancerListener
     */
    public function createLoadBalancerListener(array $options): LoadBalancerListener
    {
        return $this->model(LoadBalancerListener::class)->create($options);
    }

    /**
     * Lists loadbalancer pools.
     *
     * @return \Generator
     */
    public function listLoadBalancerPools(): \Generator
    {
        return $this->model(LoadBalancerPool::class)->enumerate($this->api->getLoadBalancerPools());
    }

    /**
     * Retrieve an instance of a loadbalancer Pool object.
     *
     * @param string $id
     *
     * @return LoadBalancerPool
     */
    public function getLoadBalancerPool(string $id): LoadBalancerPool
    {
        return $this->model(LoadBalancerPool::class, ['id' => $id]);
    }

    /**
     * Create a new loadbalancer Pool resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postLoadBalancerPool}
     *
     * @return LoadBalancerPool
     */
    public function createLoadBalancerPool(array $options): LoadBalancerPool
    {
        return $this->model(LoadBalancerPool::class)->create($options);
    }

    /**
     * Lists loadbalancer members.
     *
     * @param string $poolId
     *
     * @return \Generator
     */
    public function listLoadBalancerMembers(string $poolId): \Generator
    {
        return $this->model(LoadBalancerPool::class, ['poolId' => $poolId])->enumerate($this->api->getLoadBalancerMembers());
    }

    /**
     * Retrieve an instance of a loadbalancer Member object.
     *
     * @param string $poolId
     * @param string $memberId
     *
     * @return LoadBalancerMember
     */
    public function getLoadBalancerMember(string $poolId, string $memberId): LoadBalancerMember
    {
        return $this->model(LoadBalancerMember::class, ['poolId' => $poolId, 'id' => $memberId]);
    }

    /**
     * Create a new loadbalancer member resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postLoadBalancerMember}
     *
     * @return LoadBalancerMember
     */
    public function createLoadBalancerMember(array $options): LoadBalancerMember
    {
        return $this->model(LoadBalancerMember::class)->create($options);
    }

    /**
     * Lists loadbalancer healthmonitors.
     *
     * @return \Generator
     */
    public function listLoadBalancerHealthMonitors(): \Generator
    {
        return $this->model(LoadBalancerHealthMonitor::class)->enumerate($this->api->getLoadBalancerHealthMonitors());
    }

    /**
     * Retrieve an instance of a loadbalancer healthmonitor object.
     *
     * @param string $id
     *
     * @return LoadBalancerHealthMonitor
     */
    public function getLoadBalancerHealthMonitor(string $id): LoadBalancerHealthMonitor
    {
        return $this->model(LoadBalancerHealthMonitor::class, ['id' => $id]);
    }

    /**
     * Create a new loadbalancer healthmonitor resource.
     *
     * @param array $options {@see \OpenStack\Networking\v2\Api::postLoadBalancerHealthMonitor}
     *
     * @return LoadBalancerHealthMonitor
     */
    public function createLoadBalancerHealthMonitor(array $options): LoadBalancerHealthMonitor
    {
        return $this->model(LoadBalancerHealthMonitor::class)->create($options);
    }
}
