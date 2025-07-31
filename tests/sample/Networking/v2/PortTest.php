<?php

namespace OpenStack\Sample\Networking\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Networking\v2\Models\Port;

class PortTest extends TestCase
{
    public function testCreateBatch()
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);

        /** @var Port[] $ports */
        require_once $this->sampleFile('ports/create_batch.php', [
            '{name1}'      => $this->randomStr(),
            '{name2}'      => $this->randomStr(),
            '{networkId1}' => $network->id,
            '{networkId2}' => $network->id,
        ]);

        $this->assertCount(2, $ports);
        foreach ($ports as $port) {
            $this->assertInstanceOf(Port::class, $port);
            $this->assertNotEmpty($port->id);

            $port->delete();
        }
        $network->delete();
    }

    public function testCreatePortWithFixedIps()
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);
        $subnet = $this->getService()->createSubnet([
            'name'      => $this->randomStr(),
            'networkId' => $network->id,
            'ipVersion' => 4,
            'cidr'      => '192.168.199.0/24',
        ]);

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile('ports/create_with_fixed_ips.php', ['{networkId}' => $network->id]);

        $this->assertInstanceOf(Port::class, $port);
        $this->assertCount(2, $port->fixedIps);
        $this->assertEquals($subnet->id, $port->fixedIps[0]['subnet_id']);
        $this->assertEquals('192.168.199.100', $port->fixedIps[0]['ip_address']);
        $this->assertEquals($subnet->id, $port->fixedIps[1]['subnet_id']);
        $this->assertEquals('192.168.199.200', $port->fixedIps[1]['ip_address']);

        $port->delete();
        $subnet->delete();
        $network->delete();
    }

    public function testCreate(): Port
    {
        $network = $this->getService()->createNetwork(['name' => $this->randomStr()]);

        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile(
            'ports/create.php',
            [
                '{name}'      => $this->randomStr(),
                '{networkId}' => $network->id,
            ]);

        $this->assertInstanceOf(Port::class, $port);
        $this->assertNotEmpty($port->id);

        return $port;
    }

    /**
     * @depends testCreate
     */
    public function testList(Port $createdPort)
    {
        $found = false;
        require_once $this->sampleFile(
            'ports/list.php',
            [
                '/** @var \OpenStack\Networking\v2\Models\Port $port */' => <<<'PHP'
/** @var \OpenStack\Networking\v2\Models\Port $port */
if ($port->id === $createdPort->id) {
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
    public function testRead(Port $createdPort)
    {
        /** @var $port \OpenStack\Networking\v2\Models\Port */
        require_once $this->sampleFile(
            'ports/read.php',
            [
                '{portId}' => $createdPort->id,
            ]);

        $this->assertInstanceOf(Port::class, $port);
        $this->assertNotEmpty($port->id);
        $this->assertNotEmpty($port->name);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Port $createdPort)
    {
        $newName = $this->randomStr();

        require_once $this->sampleFile('ports/update.php', [
            '{portId}'  => $createdPort->id,
            '{newName}' => $newName,
        ]);

        $createdPort->retrieve();
        $this->assertEquals($newName, $createdPort->name);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Port $createdPort)
    {
        $network = $this->getService()->getNetwork($createdPort->networkId);

        require_once $this->sampleFile('ports/delete.php', [
            '{portId}' => $createdPort->id,
        ]);

        foreach ($this->getService()->listPorts() as $port) {
            if ($port->id === $createdPort->id) {
                $this->fail('The port was not deleted');
            }
        }

        $network->delete();

        $this->expectException(BadResponseError::class);
        $this->getService()->getSubnet($createdPort->id)->retrieve();
    }
}