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
        $this->assertEquals('146', $this->hypervisor->freeDiskGb);
        $this->assertEquals('76917', $this->hypervisor->freeRamMb);
        $this->assertEquals('localhost.localdomain', $this->hypervisor->hypervisorHostname);
        $this->assertEquals('QEMU', $this->hypervisor->hypervisorType);
        $this->assertEquals('2006000', $this->hypervisor->hypervisorVersion);
        $this->assertEquals('266', $this->hypervisor->localGb);
        $this->assertEquals('120', $this->hypervisor->localGbUsed);
        $this->assertEquals('97909', $this->hypervisor->memoryMb);
        $this->assertEquals('20992', $this->hypervisor->memoryMbUsed);
        $this->assertEquals('4', $this->hypervisor->runningVms);
        $this->assertEquals('56', $this->hypervisor->vcpus);
        $this->assertEquals('10', $this->hypervisor->vcpusUsed);
        $this->assertEquals(['host' => 'localhost.localdomain', 'id' => '8', 'disabled_reason' => null], $this->hypervisor->service);
    }
}
