<?php

namespace OpenStack\Sample\Compute\v2;


use OpenStack\Compute\v2\Models\Server;
use OpenStack\Compute\v2\Service;
use OpenStack\Networking\v2\Models\Network;
use RuntimeException;

abstract class TestCase extends \OpenStack\Sample\TestCase
{
    protected function getService(): Service
    {
        return $this->getCachedService(Service::class);
    }

    protected function getServiceNetwork(): \OpenStack\Networking\v2\Service
    {
        return $this->getCachedService(\OpenStack\Networking\v2\Service::class);
    }

    protected function searchImageId(): string
    {
        foreach ($this->getService()->listImages() as $image) {
            if (str_starts_with($image->name, 'cirros')) {
                return $image->id;
            }
        }

        throw new RuntimeException('Unable to find image "cirros". Make sure this image is available for integration test.');
    }

    protected function sampleFile(string $path, array $replacements = []): string
    {
        return parent::sampleFile("Compute/v2/$path", $replacements);
    }

    /**
     * Creates a server and all dependencies for testing
     */
    protected function createServer(): Server
    {
        $flavorId = getenv('OS_FLAVOR');

        if (!$flavorId) {
            throw new RuntimeException('OS_FLAVOR env var must be set');
        }

        $network = $this->getServiceNetwork()->createNetwork(['name' => $this->randomStr()]);
        $this->getServiceNetwork()->createSubnet(
            [
                'name'      => $this->randomStr(),
                'networkId' => $network->id,
                'ipVersion' => 4,
                'cidr'      => '10.20.30.0/24',
            ]
        );

        $server = $this->getService()->createServer(
            [
                'name'      => $this->randomStr(),
                'imageId'   => $this->searchImageId(),
                'flavorId'  => $flavorId,
                'networks'  => [
                    ['uuid' => $network->id],
                ],
            ]
        );

        $server->waitUntilActive(60);

        return $server;
    }

    /**
     * Deletes server and all dependencies
     */
    protected function deleteServer(Server $server): void
    {
        $server->delete();
        $server->waitUntilDeleted();

        foreach (array_keys($server->addresses) as $networkName) {
            $network = $this->getServiceNetwork()->listNetworks(['name' => $networkName])->current();
            $this->deleteNetwork($network);
        }
    }

    /**
     * Deletes network and all dependencies
     */
    protected function deleteNetwork(Network $network): void
    {
        foreach ($network->subnets as $subnetId) {
            $subnet = $this->getServiceNetwork()->getSubnet($subnetId);
            $subnet->delete();
        }

        foreach ($this->getServiceNetwork()->listPorts(['networkId' => $network->id]) as $port) {
            if ($port->deviceOwner) {
                continue;
            }

            $port->delete();
            $port->waitUntilDeleted();
        }

        $network->delete();
    }
}