<?php

namespace OpenStack\Sample\Networking\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp;
use OpenStack\Networking\v2\Models\Port;
use OpenStack\Networking\v2\Models\Subnet;

class FloatingIpTest extends TestCase
{
    private function findSubnetIp(Port $port, Subnet $subnet): string
    {
        foreach ($port->fixedIps as $fixedIp) {
            if ($fixedIp['subnet_id'] == $subnet->id) {
                return $fixedIp['ip_address'];
            }
        }

        throw new \RuntimeException("Unable to find IP address for port {$port->id} on subnet {$subnet->id}");
    }

    public function testCreate(): FloatingIpData
    {
        $data = new FloatingIpData();
        $data->externalNetwork = $this->getService()->createNetwork([
            'name'             => $this->randomStr(),
            'routerAccessible' => true,
        ]);
        $data->externalSubnet = $this->getService()->createSubnet([
            'networkId' => $data->externalNetwork->id,
            'name'      => $this->randomStr(),
            'ipVersion' => 4,
            'cidr'      => '10.0.0.0/24',
        ]);

        $data->internalNetwork = $this->getService()->createNetwork([
            'name'             => $this->randomStr(),
            'routerAccessible' => false,
        ]);
        $data->internalSubnet = $this->getService()->createSubnet([
            'networkId' => $data->internalNetwork->id,
            'name'      => $this->randomStr(),
            'ipVersion' => 4,
            'cidr'      => '192.168.199.0/24',
        ]);


        $data->externalNetwork->waitUntilActive();
        $data->internalNetwork->waitUntilActive();

        $data->router = $this->getService()->createRouter([
            'name'                => $this->randomStr(),
            'externalGatewayInfo' => [
                'networkId'  => $data->externalNetwork->id,
                'enableSnat' => true,
            ],
        ]);
        $data->router->addInterface(['subnetId' => $data->internalSubnet->id]);

        $data->port = $this->getService()->createPort([
            'networkId' => $data->internalNetwork->id,
            'name'      => $this->randomStr(),
        ]);
        $fixedIp = $this->findSubnetIp($data->port, $data->internalSubnet);

        /** @var FloatingIp $floatingIp */
        require_once $this->sampleFile('floatingIPs/create.php', [
            '{networkId}'      => $data->externalNetwork->id,
            '{portId}'         => $data->port->id,
            '{fixedIpAddress}' => $fixedIp,
        ]);
        $this->assertInstanceOf(FloatingIp::class, $floatingIp);
        $this->assertEquals($data->externalNetwork->id, $floatingIp->floatingNetworkId);
        $this->assertEquals($data->port->id, $floatingIp->portId);

        $data->floatingIp = $floatingIp;

        /*
        $port->delete();
        $router->delete();
        $internalSubnet->delete();
        $internalNetwork->delete();
        $externalSubnet->delete();
        $externalNetwork->delete();
        */

        return $data;
    }

    /**
     * @depends testCreate
     */
    public function testList(FloatingIpData $data)
    {
        $found = false;
        require_once $this->sampleFile(
            'floatingIPs/list.php',
            [
                '/** @var \OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp $floatingIp */' => <<<'PHP'
/** @var \OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp $floatingIp */
if ($floatingIp->id == $data->floatingIp->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }


    /**
     * @depends testCreate
     */
    public function testGet(FloatingIpData $data)
    {
        /** @var FloatingIp $floatingIp */
        require_once $this->sampleFile('floatingIPs/get.php', [
            '{id}' => $data->floatingIp->id,
        ]);
        $this->assertInstanceOf(FloatingIp::class, $floatingIp);
        $this->assertEquals($data->floatingIp->id, $floatingIp->id);
        $this->assertEmpty($floatingIp->portId);

        $floatingIp->retrieve();
        $this->assertEquals($data->floatingIp->portId, $floatingIp->portId);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(FloatingIpData $data)
    {
        $newPort = $this->getService()->createPort([
            'networkId' => $data->internalNetwork->id,
            'name'      => $this->randomStr(),
        ]);

        $this->assertNotEquals($newPort->id, $data->port->id);

        require_once $this->sampleFile('floatingIPs/update.php', [
            '{id}'            => $data->floatingIp->id,
            '{newPortId}'     => $newPort->id,
        ]);

        $data->floatingIp->retrieve();
        $this->assertEquals($newPort->id, $data->floatingIp->portId);

        $data->port->delete();
        $data->port = $newPort;
    }

    /**
     * @depends testCreate
     */
    public function testDelete(FloatingIpData $data)
    {
        require_once $this->sampleFile('floatingIPs/delete.php', [
            '{id}' => $data->floatingIp->id,
        ]);

        foreach ($this->getService()->listFloatingIps() as $floatingIp) {
            if ($floatingIp->id == $data->floatingIp->id) {
                $this->fail('Floating IP still exists');
            }
        }

        $data->router->removeInterface(['subnetId' => $data->internalSubnet->id]);
        $data->router->delete();
        $data->router->waitUntilDeleted();

        $data->port->delete();
        $data->internalSubnet->delete();
        $data->internalNetwork->delete();
        $data->externalSubnet->delete();
        $data->externalNetwork->delete();

        $this->expectException(BadResponseError::class);
        $data->floatingIp->retrieve();
    }



}