<?php

namespace OpenStack\Test\Compute\v2\Models;

use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Hypervisor;
use OpenStack\Test\TestCase;

class HypervisorTest extends TestCase
{
    /**@var Hypervisor */
    private $hypervisor;

    const ID = 1;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->hypervisor = new Hypervisor($this->client->reveal(), new Api());
        $this->hypervisor->id = self::ID;
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'os-hypervisors/' . self::ID, null, [], 'hypervisor-get');

        $this->hypervisor->retrieve();

        $this->assertEquals('1', $this->hypervisor->id);
        $this->assertEquals('enabled', $this->hypervisor->status);
        $this->assertEquals('up', $this->hypervisor->state);

        $this->assertEquals('146', $this->hypervisor->free_disk_gb);
        $this->assertEquals('76917', $this->hypervisor->free_ram_mb);
        $this->assertEquals('localhost.localdomain', $this->hypervisor->hypervisor_hostname);
        $this->assertEquals('QEMU', $this->hypervisor->hypervisor_type);
        $this->assertEquals('2006000', $this->hypervisor->hypervisor_version);
        $this->assertEquals('266', $this->hypervisor->local_gb);
        $this->assertEquals('120', $this->hypervisor->local_gb_used);
        $this->assertEquals('97909', $this->hypervisor->memory_mb);
        $this->assertEquals('20992', $this->hypervisor->memory_mb_used);
        $this->assertEquals('4', $this->hypervisor->running_vms);
        $this->assertEquals('56', $this->hypervisor->vcpus);
        $this->assertEquals('10', $this->hypervisor->vcpus_used);
        $this->assertEquals(['host' => 'localhost.localdomain', 'id' => '8', 'disabled_reason' => null], $this->hypervisor->service);
    }
}
