<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Compute\v2\Models\Hypervisor;
use OpenStack\Compute\v2\Models\HypervisorStatistic;

class HypervisorTest extends TestCase
{
    public function testList()
    {
        require_once $this->sampleFile(
            'hypervisors/list_hypervisors.php',
            [
                '/** @var \OpenStack\Compute\v2\Models\Hypervisor $hypervisor */' => <<<'PHP'
/** @var \OpenStack\Compute\v2\Models\Hypervisor $hypervisor */
$this->assertInstanceOf(\OpenStack\Compute\v2\Models\Hypervisor::class, $hypervisor);
PHP
                ,
            ]
        );
    }

    public function testGet()
    {
        /** @var \OpenStack\Compute\v2\Models\Hypervisor $hypervisor */
        require_once $this->sampleFile('hypervisors/get_hypervisor.php', ['{hypervisorId}' => '1']);

        $this->assertInstanceOf(Hypervisor::class, $hypervisor);
        $this->assertEquals(1, $hypervisor->id);
    }

    public function testGetStatistics()
    {
        /** @var \OpenStack\Compute\v2\Models\HypervisorStatistic $hypervisorStatistics */
        require_once $this->sampleFile('hypervisors/get_hypervisors_statistics.php', []);

        $this->assertInstanceOf(HypervisorStatistic::class, $hypervisorStatistics);
    }
}