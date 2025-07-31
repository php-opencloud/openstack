<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Networking\v2\Extensions\SecurityGroups\Service;

class SecurityGroupTest extends TestCase
{
    /**
     * @return array{0: \OpenStack\Compute\v2\Models\Server, 1: \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup}
     */
    public function testAdd(): array
    {
        $createdServer = $this->createServer();
        $createdSecurityGroup = $this->getNetworkService()
            ->createSecurityGroup(['name' => $this->randomStr()]);

        $replacements = [
            '{serverId}'     => $createdServer->id,
            '{secGroupName}' => $createdSecurityGroup->name,
        ];

        require_once $this->sampleFile('servers/add_security_group.php', $replacements);

        $found = false;
        foreach ($createdServer->listSecurityGroups() as $securityGroup) {
            if ($securityGroup->name === $createdSecurityGroup->name) {
                $found = true;
            }
        }
        $this->assertTrue($found);

        return [$createdServer, $createdSecurityGroup];
    }


    /**
     * @depends testAdd
     */
    public function testList(array $data)
    {
        $found = false;
        require_once $this->sampleFile(
            'servers/list_security_groups.php',
            [
                '{serverId}' => $data[0]->id,
                '/** @var \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup $securityGroup */' => <<<'PHP'
/** @var \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup $securityGroup */
if($securityGroup->name === $data[1]->name) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testAdd
     */
    public function testRemove(array $data)
    {
        require_once $this->sampleFile('servers/remove_security_group.php', [
            '{serverId}'     => $data[0]->id,
            '{secGroupName}' => $data[1]->name,
        ]);

        $found = false;
        foreach ($data[0]->listSecurityGroups() as $securityGroup) {
            if ($securityGroup->name === $data[1]->name) {
                $found = true;
            }
        }
        $this->assertFalse($found);

        $data[1]->delete();
        $data[0]->delete();
    }

}