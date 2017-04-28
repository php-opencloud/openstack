<?php declare(strict_types=1);

namespace OpenStack\Networking\v2;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Networking\v2\Models\Network;
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
     * Lists default quotas for a project
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
}
