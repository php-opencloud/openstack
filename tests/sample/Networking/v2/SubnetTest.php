<?php

namespace OpenStack\Sample\Networking\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Networking\v2\Models\Subnet;

class SubnetTest extends TestCase
{
    public function testCreateBatch()
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);

        /** @var Subnet[] $subnets */
        require_once $this->sampleFile('subnets/create_batch.php', [
            '{subnetName1}' => $this->randomStr(),
            '{subnetName2}' => $this->randomStr(),
            '{networkId1}'  => $network->id,
            '{networkId2}'  => $network->id,
        ]);

        foreach ($subnets as $subnet) {
            $this->assertInstanceOf(Subnet::class, $subnet);
            $this->assertNotEmpty($subnet->id);

            $subnet->delete();
        }
        $network->delete();
    }

    public function testCreateWithHostRoute()
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/create_with_host_routes.php', [
            '{subnetName}' => $this->randomStr(),
            '{networkId}'  => $network->id,
        ]);

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertNotEmpty($subnet->id);

        $network->delete();
    }

    public function testCreateWithGatewayIp()
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/create_with_gateway_ip.php', [
            '{subnetName}' => $this->randomStr(),
            '{networkId}'  => $network->id,
        ]);

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertNotEmpty($subnet->id);
        $this->assertEquals('192.168.199.128', $subnet->gatewayIp);

        $subnet->delete();
        $network->delete();
    }

    public function testCreate(): Subnet
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);

        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile(
            'subnets/create.php',
            [
                '{subnetName}' => $this->randomStr(),
                '{networkId}'  => $network->id,
            ]
        );

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertNotEmpty($subnet->id);

        return $subnet;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Subnet $createdSubnet)
    {
        $newName = $this->randomStr();

        require_once $this->sampleFile('subnets/update.php', [
            '{subnetId}' => $createdSubnet->id,
            '{newName}'  => $newName,
        ]);

        $createdSubnet->retrieve();
        $this->assertEquals($newName, $createdSubnet->name);
    }

    /**
     * @depends testCreate
     */
    public function testRead(Subnet $createdSubnet)
    {
        /** @var $subnet \OpenStack\Networking\v2\Models\Subnet */
        require_once $this->sampleFile('subnets/read.php', ['{subnetId}' => $createdSubnet->id]);

        $this->assertInstanceOf(Subnet::class, $subnet);
        $this->assertEquals($subnet->id, $subnet->id);
        $this->assertEquals($subnet->name, $subnet->name);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Subnet $createdSubnet)
    {
        $network = $this->getService()->getNetwork($createdSubnet->networkId);

        require_once $this->sampleFile('subnets/delete.php', ['{subnetId}' => $createdSubnet->id]);

        foreach ($this->getService()->listSubnets() as $subnet) {
            if ($subnet->id == $createdSubnet->id) {
                $this->fail('The deleted subnet still exists');
            }
        }

        $network->delete();

        $this->expectException(BadResponseError::class);
        $this->getService()->getSubnet($createdSubnet->id)->retrieve();
    }
}