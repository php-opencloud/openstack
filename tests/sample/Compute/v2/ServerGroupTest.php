<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Compute\v2\Models\ServerGroup;

class ServerGroupTest extends TestCase
{
    private function assertPolicy(ServerGroup $serverGroup, string $expected): void
    {
        $this->assertContains($expected, $serverGroup->policies);
        $this->assertEquals($expected, $serverGroup->policy);
    }

    public function testCreateWithMicroversion264()
    {
        $name = $this->randomStr();

        /** @var ServerGroup $serverGroup */
        require_once $this->sampleFile('server_groups/create_2_64.php', ['{serverGroupName}' => $name]);

        try {
            $this->assertInstanceOf(ServerGroup::class, $serverGroup);
            $this->assertEquals($name, $serverGroup->name);
            $this->assertPolicy($serverGroup, 'anti-affinity');
            $this->assertEquals(1, $serverGroup->rules['max_server_per_host']);
        } finally {
            $serverGroup->delete();
        }

        $this->expectException(BadResponseError::class);
        $serverGroup->retrieve();
    }

    public function testCreate(): ServerGroup
    {
        $name = $this->randomStr();

        /** @var ServerGroup $serverGroup */
        require_once $this->sampleFile('server_groups/create.php', ['{serverGroupName}' => $name]);

        $this->assertInstanceOf(ServerGroup::class, $serverGroup);
        $this->assertEquals($name, $serverGroup->name);
        $this->assertPolicy($serverGroup, 'affinity');

        return $serverGroup;
    }

    /**
     * @depends testCreate
     */
    public function testList(ServerGroup $createdServerGroup)
    {
        $found = false;
        require_once $this->sampleFile(
            'server_groups/list.php',
            [
                '/** @var \OpenStack\Compute\v2\Models\ServerGroup $serverGroup */' => <<<'PHP'
/** @var \OpenStack\Compute\v2\Models\ServerGroup $serverGroup */
if ($serverGroup->id === $createdServerGroup->id) {
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
    public function testRead(ServerGroup $createdServerGroup)
    {
        /** @var ServerGroup $serverGroup */
        require_once $this->sampleFile('server_groups/read.php', ['{serverGroupId}' => $createdServerGroup->id]);

        $this->assertInstanceOf(ServerGroup::class, $serverGroup);
        $this->assertEquals($createdServerGroup->id, $serverGroup->id);
        $this->assertEquals($createdServerGroup->name, $serverGroup->name);
        $this->assertPolicy($serverGroup, 'affinity');
    }

    /**
     * @depends testCreate
     */
    public function testDelete(ServerGroup $createdServerGroup)
    {
        require_once $this->sampleFile('server_groups/delete.php', ['{serverGroupId}' => $createdServerGroup->id]);

        foreach ($this->getService()->listServerGroups() as $serverGroup) {
            $this->assertNotEquals($createdServerGroup->id, $serverGroup->id);
        }

        $this->expectException(BadResponseError::class);
        $createdServerGroup->retrieve();
    }
}
