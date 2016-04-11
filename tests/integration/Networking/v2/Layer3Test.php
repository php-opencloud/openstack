<?php

namespace OpenStack\Integration\Networking\v2;

use OpenCloud\Integration\TestCase;
use OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp;
use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Models\Port;
use OpenStack\Networking\v2\Models\Subnet;

class Layer3Test extends TestCase
{
    public function getService(): \OpenStack\Networking\v2\Extensions\Layer3\Service
    {
        return $this->getBaseClient()->networkingV2ExtLayer3();
    }

    private function getV2Service(): \OpenStack\Networking\v2\Service
    {
        return $this->getBaseClient()->networkingV2();
    }

    public function runTests()
    {
        $this->startTimer();
        $this->floatingIps();
        $this->outputTimeTaken();
    }

    public function teardown()
    {
        parent::teardown();

        $this->deleteItems($this->getV2Service()->listPorts());
        $this->deleteItems($this->getV2Service()->listNetworks());
        $this->deleteItems($this->getService()->listFloatingIps());
    }

    private function createNetwork(): Network
    {
        $network = $this->getV2Service()->createNetwork(['name' => $this->randomStr(), 'routerAccessible' => true]);
        $network->waitUntilActive();
        return $network;
    }

    private function createSubnet(Network $network): Subnet
    {
        return $this->getV2Service()->createSubnet([
            'networkId' => $network->id,
            'name'      => $this->randomStr(),
            'ipVersion' => 4,
            'cidr'      => '192.168.199.0/24',
        ]);
    }

    private function createPort(Network $network): Port
    {
        return $this->getV2Service()->createPort(['networkId' => $network->id, 'name' => $this->randomStr()]);
    }

    public function floatingIps()
    {
        $this->logStep('Creating network');
        $network = $this->createNetwork();

        $this->logStep('Creating subnet for network %id%', ['%id%' => $network->id]);
        $this->createSubnet($network);

        $this->logStep('Creating port for network %id%', ['%id%' => $network->id]);
        $port1 = $this->createPort($network);

        $replacements = [
            '{networkId}' => $network->id,
            '{portId}'    => $port1->id,
        ];

        $this->logStep('Create floating IP');
        /** @var FloatingIp $ip */
        $path = $this->sampleFile($replacements, 'floatingIPs/create.php');
        require_once $path;
        $this->assertInstanceOf(FloatingIp::class, $ip);
        $this->assertEquals($network->id, $ip->floatingNetworkId);
        $this->assertEquals($port1->id, $ip->portId);

        $this->logStep('List floating IPs');
        $path = $this->sampleFile($replacements, 'floatingIPs/list.php');
        require_once $path;

        $this->logStep('Get floating IP');
        $replacements['{id}'] = $ip->id;
        $path = $this->sampleFile($replacements, 'floatingIPs/get.php');
        require_once $path;
        $this->assertInstanceOf(FloatingIp::class, $ip);

        $this->logStep('Update floating IP');
        $port2 = $this->createPort($network);
        $replacements['{newPortId}'] = $port2->id;
        $path = $this->sampleFile($replacements, 'floatingIPs/update.php');
        require_once $path;

        $this->logStep('Delete floating IP');
        $path = $this->sampleFile($replacements, 'floatingIPs/update.php');
        require_once $path;

        $port1->delete();
        $port2->delete();
        $network->delete();
    }
}
