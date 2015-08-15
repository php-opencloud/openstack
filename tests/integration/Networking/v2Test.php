<?php

namespace OpenStack\Integration\Networking;

use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Models\Subnet;
use OpenStack\Integration\TestCase;
use OpenStack\OpenStack;

class V2Test extends TestCase
{
    private $service;
    private $networkId;
    private $subnetId;

    private function getService()
    {
        if (null === $this->service) {
            $this->service = (new OpenStack())->networkingV2(['region' => getenv('OS_REGION')]);
        }

        return $this->service;
    }

    protected function getBasePath()
    {
        return __DIR__ . '/../../../samples/networking/v2/';
    }

    public function runTests()
    {
        $this->startTimer();

        $this->createNetworksAndDelete();

        $this->createNetwork();

        try {
            $this->updateNetwork();
            $this->retrieveNetwork();

            $this->createSubnetsAndDelete();

            $this->createSubnet();
            $this->updateSubnet();
            $this->retrieveSubnet();
            $this->deleteSubnet();

            $this->createSubnetWithGatewayIp();
            $this->deleteSubnet();

            $this->createSubnetWithHostRoutes();
            $this->deleteSubnet();

        } finally {
            // Teardown
            $this->deleteNetwork();
        }

        $this->outputTimeTaken();
    }

    private function createNetworksAndDelete()
    {
        $replacements = [
            '{networkName1}' => $this->randomStr(),
            '{networkName2}' => $this->randomStr()
        ];

        /** @var $networks array */
        $path = $this->sampleFile($replacements, 'create_networks.php');
        require_once $path;

        foreach($networks as $network) {
            $this->assertInstanceOf(Network::class, $network);
            $this->assertNotEmpty($network->id);

            $this->networkId = $network->id;
            $this->logStep('Created network {id}', ['{id}' => $this->networkId]);

            $this->deleteNetwork();
        }

        $this->networkId = null;
    }

    private function createNetwork()
    {
        $replacements = [
            '{networkName}' => $this->randomStr(),
        ];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'create_network.php');
        require_once $path;

        $this->assertInstanceOf(Network::class, $network);
        $this->assertNotEmpty($network->id);

        $this->networkId = $network->id;

        $this->logStep('Created network {id}', ['{id}' => $this->networkId]);
    }

    private function updateNetwork()
    {
        $name = $this->randomStr();

        $replacements = [
            '{networkId}' => $this->networkId,
            '{newName}'  => $name,
        ];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'update_network.php');
        require_once $path;

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals($name, $network->name);

        $this->logStep('Updated network ID to use this name: NAME', ['ID' => $this->networkId, 'NAME' => $name]);
    }

    private function retrieveNetwork()
    {
        $replacements = ['{networkId}' => $this->networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'get_network.php');
        require_once $path;

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals($this->networkId, $network->id);

        $this->logStep('Retrieved the details of network ID', ['ID' => $this->networkId]);
    }

    private function deleteNetwork()
    {
        $replacements = ['{networkId}' => $this->networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'delete_network.php');
        require_once $path;

        $this->logStep('Deleted network ID', ['ID' => $this->networkId]);
    }

    private function createSubnetsAndDelete()
    {
        $replacements = [
            '{subnetName1}' => $this->randomStr(),
            '{subnetName2}' => $this->randomStr(),
            '{networkId1}' => $this->networkId,
            '{networkId2}' => $this->networkId,
        ];

        /** @var $subnets array */
        $path = $this->sampleFile($replacements, 'create_subnets.php');
        require_once $path;

        foreach($subnets as $subnet) {
            $this->assertInstanceOf(Subnet::class, $subnet);
            $this->assertNotEmpty($subnet->id);

            $this->subnetId = $subnet->id;
            $this->logStep('Created subnet {id}', ['{id}' => $this->subnetId]);

            $this->deleteSubnet();
        }

        $this->subnetId = null;
    }

    private function createSubnet()
    {
        $replacements = [
            '{subnetName}' => $this->randomStr(),
            '{networkId}' => $this->networkId,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        $path = $this->sampleFile($replacements, 'create_subnet.php');
        require_once $path;

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertNotEmpty($subnet->id);

        $this->subnetId = $subnet->id;

        $this->logStep('Created subnet {id}', ['{id}' => $this->subnetId]);
    }

    private function createSubnetWithGatewayIp()
    {
        $replacements = [
            '{networkId}' => $this->networkId,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        $path = $this->sampleFile($replacements, 'create_subnet_with_gateway_ip.php');
        require_once $path;

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertNotEmpty($subnet->id);

        $this->subnetId = $subnet->id;

        $this->logStep('Created subnet {id} with gateway ip', ['{id}' => $this->subnetId]);
    }

    private function createSubnetWithHostRoutes()
    {
        $replacements = [
            '{networkId}' => $this->networkId,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        $path = $this->sampleFile($replacements, 'create_subnet_with_host_routes.php');
        require_once $path;

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertNotEmpty($subnet->id);

        $this->subnetId = $subnet->id;

        $this->logStep('Created subnet {id} with host routes', ['{id}' => $this->subnetId]);
    }

    private function updateSubnet()
    {
        $name = $this->randomStr();

        $replacements = [
            '{subnetId}' => $this->subnetId,
            '{newName}'  => $name,
        ];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        $path = $this->sampleFile($replacements, 'update_subnet.php');
        require_once $path;

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertEquals($name, $subnet->name);

        $this->logStep('Updated subnet ID to use this name: NAME', ['ID' => $this->subnetId, 'NAME' => $name]);
    }


    private function retrieveSubnet()
    {
        $replacements = ['{subnetId}' => $this->subnetId];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        $path = $this->sampleFile($replacements, 'get_subnet.php');
        require_once $path;

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertEquals($this->subnetId, $subnet->id);

        $this->logStep('Retrieved the details of subnet ID', ['ID' => $this->subnetId]);
    }

    private function deleteSubnet()
    {
        $replacements = ['{subnetId}' => $this->subnetId];

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        $path = $this->sampleFile($replacements, 'delete_subnet.php');
        require_once $path;

        $this->logStep('Deleted subnet ID', ['ID' => $this->subnetId]);
    }
}
