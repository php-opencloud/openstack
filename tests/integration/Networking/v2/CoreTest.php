<?php

namespace OpenStack\Integration\Networking\v2;

use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Models\Port;
use OpenStack\Networking\v2\Models\Subnet;
use OpenStack\Integration\TestCase;

class CoreTest extends TestCase
{
    public function runTests()
    {
        $this->startTimer();

        $this->networks();
        $this->subnets();
        $this->ports();

        $this->outputTimeTaken();
    }

    public function subnets()
    {
        $this->createSubnetsAndDelete();
        $this->createSubnetWithHostRoutes();

        [$subnetId, $networkId] = $this->createSubnet();

        $this->updateSubnet($subnetId);
        $this->retrieveSubnet($subnetId);
        $this->deleteSubnet($subnetId);
        $this->deleteNetwork($networkId);
    }

    public function networks()
    {
        $this->createNetworksAndDelete();

        $networkId = $this->createNetwork();
        $this->updateNetwork($networkId);
        $this->retrieveNetwork($networkId);
        $this->deleteNetwork($networkId);
    }

    private function createNetworksAndDelete()
    {
        $replacements = [
            '{networkName1}' => $this->randomStr(),
            '{networkName2}' => $this->randomStr()
        ];

        /** @var $networks array */
        require_once $this->sampleFile('networks/create_batch.php', $replacements);

        foreach ($networks as $network) {
            self::assertInstanceOf(Network::class, $network);
            self::assertNotEmpty($network->id);

            $this->networkId = $network->id;
            $this->logStep('Created network {id}', ['{id}' => $this->networkId]);

            $this->deleteNetwork($network->id);
        }
    }

    private function createNetwork()
    {
        $replacements = [
            '{networkName}' => $this->randomStr(),
        ];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', $replacements);

        self::assertInstanceOf(Network::class, $network);
        self::assertNotEmpty($network->id);

        $this->logStep('Created network {id}', ['{id}' => $this->networkId]);

        return $network->id;
    }

    private function updateNetwork($networkId)
    {
        $name = $this->randomStr();

        $replacements = [
            '{networkId}' => $networkId,
            '{newName}'   => $name,
        ];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/update.php', $replacements);

        self::assertInstanceOf(Network::class, $network);
        self::assertEquals($name, $network->name);

        $this->logStep('Updated network ID to use this name: NAME', ['ID' => $networkId, 'NAME' => $name]);
    }

    private function retrieveNetwork($networkId)
    {
        $replacements = ['{networkId}' => $networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/get.php', $replacements);

        self::assertInstanceOf(Network::class, $network);

        $this->logStep('Retrieved the details of network ID', ['ID' => $networkId]);
    }

    private function deleteNetwork($networkId)
    {
        $replacements = ['{networkId}' => $networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/delete.php', $replacements);

        $this->logStep('Deleted network ID', ['ID' => $networkId]);
    }

    private function createSubnetsAndDelete()
    {
        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', ['{newName}' => $this->randomStr()]);

        $replacements = [
            '{subnetName1}' => $this->randomStr(),
            '{subnetName2}' => $this->randomStr(),
            '{networkId1}'  => $network->id,
            '{networkId2}'  => $network->id,
        ];

        /** @var $subnets array */
        require_once $this->sampleFile('subnets/create_batch.php', $replacements);

        foreach ($subnets as $subnet) {
            self::assertInstanceOf(Subnet::class, $subnet);
            self::assertNotEmpty($subnet->id);

            $this->logStep('Created subnet {id}', ['{id}' => $subnet->id]);

            $this->deleteSubnet($subnet->id);
        }

        require_once $this->sampleFile('networks/delete.php', ['{networkId}' => $network->id]);
    }

    private function createSubnet()
    {
        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', ['{newName}' => $this->randomStr()]);

        $replacements = [
            '{subnetName}' => $this->randomStr(),
            '{networkId}'  => $network->id,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/create.php', $replacements);

        self::assertInstanceOf(Subnet::class, $subnet);
        self::assertNotEmpty($subnet->id);

        $this->logStep('Created subnet {id}', ['{id}' => $subnet->id]);

        return [$subnet->id, $network->id];
    }

    private function createSubnetWithGatewayIp()
    {
        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', ['{newName}' => $this->randomStr()]);

        $replacements = [
            '{networkId}' => $network->id,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/create_with_gateway_ip.php', $replacements);

        self::assertInstanceOf(Subnet::class, $subnet);
        self::assertNotEmpty($subnet->id);

        $this->subnetId = $subnet->id;

        $this->logStep('Created subnet {id} with gateway ip', ['{id}' => $this->subnetId]);

        require_once $this->sampleFile('networks/delete.php', $replacements);
    }

    private function createSubnetWithHostRoutes()
    {
        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', ['{newName}' => $this->randomStr()]);

        $replacements = [
            '{networkId}' => $network->id,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/create_with_host_routes.php', $replacements);

        self::assertInstanceOf(Subnet::class, $subnet);
        self::assertNotEmpty($subnet->id);

        $this->logStep('Created subnet {id} with host routes', ['{id}' => $subnet->id]);

        require_once $this->sampleFile('networks/delete.php', $replacements);
    }

    private function updateSubnet($subnetId)
    {
        $name = $this->randomStr();

        $replacements = [
            '{subnetId}' => $subnetId,
            '{newName}'  => $name,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/update.php', $replacements);

        self::assertInstanceOf(Subnet::class, $subnet);
        self::assertEquals($name, $subnet->name);

        $this->logStep('Updated subnet ID to use this name: NAME', ['ID' => $subnetId, 'NAME' => $name]);
    }


    private function retrieveSubnet($subnetId)
    {
        $replacements = ['{subnetId}' => $subnetId];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/get.php', $replacements);

        self::assertInstanceOf(Subnet::class, $subnet);

        $this->logStep('Retrieved the details of subnet ID', ['ID' => $subnetId]);
    }

    private function deleteSubnet($subnetId)
    {
        $replacements = ['{subnetId}' => $subnetId];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/delete.php', $replacements);

        $this->logStep('Deleted subnet ID', ['ID' => $subnetId]);
    }

    public function ports()
    {
        $this->logStep('Test port');

        $replacements = ['{newName}' => $this->randomStr()];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', $replacements);

        $replacements = ['{networkId}' => $network->id];

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile('ports/create.php', $replacements);

        $replacements['{portId}'] = $port->id;
        $port->networkId = $network->id;

        /** @var $ports array */
        require_once $this->sampleFile('ports/create_batch.php', $replacements);
        foreach ($ports as $port) {
            self::assertInstanceOf(Port::class, $port);
            $port->delete();
        }

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile('ports/list.php', $replacements);

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile('ports/get.php', $replacements);
        self::assertInstanceOf(Port::class, $port);

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile('ports/update.php', $replacements);
        self::assertInstanceOf(Port::class, $port);

        require_once $this->sampleFile('ports/delete.php', $replacements);

        require_once $this->sampleFile('networks/delete.php', $replacements);

        $this->createPortWithFixedIps();
    }

    private function createPortWithFixedIps()
    {
        $this->logStep('Test port with fixed IP');

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/create.php', ['{networkName}' => $this->randomStr()]);
        $this->logStep('Created network {id}', ['{id}' => $network->id]);


        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/create.php', ['{subnetName}' => $this->randomStr(), '{networkId}' => $network->id]);
        $this->logStep('Created subnet {id}', ['{id}' => $subnet->id]);

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile('ports/create_with_fixed_ips.php', ['{networkId}' => $network->id]);
        $this->logStep('Created port {id}', ['{id}' => $port->id]);

        require_once $this->sampleFile('ports/delete.php', ['{portId}' => $port->id]);

        $this->logStep('Deleted port {id}', ['{id}' => $port->id]);

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/delete.php', ['{subnetId}' => $subnet->id]);
        $this->logStep('Deleted subnet {id}', ['{id}' => $subnet->id]);

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        require_once $this->sampleFile('networks/delete.php', ['{networkId}' => $network->id]);
        $this->logStep('Deleted network {id}', ['{id}' => $network->id]);
    }
}
