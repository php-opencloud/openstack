<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Compute\v2\Models\Server;
use RuntimeException;

class ServerTest extends TestCase
{
    public function testCreate(): Server
    {
        $flavorId = getenv('OS_FLAVOR');

        if (!$flavorId) {
            throw new RuntimeException('OS_FLAVOR env var must be set');
        }

        $network = $this->getNetworkService()->createNetwork(['name' => $this->randomStr()]);
        $this->getNetworkService()->createSubnet(
            [
                'name'      => $this->randomStr(),
                'networkId' => $network->id,
                'ipVersion' => 4,
                'cidr'      => '10.20.30.0/24',
            ]
        );

        $replacements = [
            '{serverName}' => $this->randomStr(),
            '{imageId}'    => $this->searchImageId(),
            '{flavorId}'   => $flavorId,
            '{networkId}'  => $network->id,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/create.php', $replacements);

        $server->waitUntilActive();

        $this->assertInstanceOf(Server::class, $server);
        $this->assertNotEmpty($server->id);
        $this->assertNotEmpty($server->adminPass);

        return $server;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Server $createdServer)
    {
        $newName = $this->randomStr();

        require_once $this->sampleFile('servers/update.php', [
            '{serverId}' => $createdServer->id,
            '{newName}'  => $newName,
        ]);

        $createdServer->waitUntilActive(60);
        $createdServer->retrieve();

        $this->assertEquals($newName, $createdServer->name);
    }

    /**
     * @depends testCreate
     */
    public function testGet(Server $createdServer)
    {
        /** @var \OpenStack\Compute\v2\Models\Server $server */
        require_once $this->sampleFile('servers/read.php', ['{serverId}' => $createdServer->id]);

        $this->assertInstanceOf(Server::class, $server);
        $this->assertEquals($createdServer->id, $server->id);
        $this->assertEquals($createdServer->name, $server->name);
        $this->assertNotNull($server->created);
        $this->assertNotNull($server->updated);
        $this->assertNotNull($server->name);
        $this->assertNotNull($server->ipv4);
        $this->assertNotNull($server->status);
        $this->assertInstanceOf(Image::class, $server->image);
        $this->assertInstanceOf(Flavor::class, $server->flavor);
    }

    /**
     * @depends testCreate
     */
    public function testMergeMetadata(Server $createdServer)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();

        $createdServer->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'servers/merge_server_metadata.php',
            [
                '{serverId}' => $createdServer->id,
                '{key}'      => 'Foo',
                '{value}'    => $fooVal,
            ]
        );

        $metadata = $createdServer->getMetadata();
        $this->assertEquals($initVal, $metadata['Init']);
        $this->assertEquals($fooVal, $metadata['Foo']);
    }

    /**
     * @depends testCreate
     * @depends testMergeMetadata
     */
    public function testGetMetadata(Server $createdServer)
    {
        /** @var array $metadata */
        require_once $this->sampleFile('servers/get_server_metadata.php', ['{serverId}' => $createdServer->id]);

        $this->assertArrayHasKey('Init', $metadata);
        $this->assertArrayHasKey('Foo', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testResetMetadata(Server $createdServer)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();

        $createdServer->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'servers/reset_server_metadata.php',
            [
                '{serverId}' => $createdServer->id,
                '{key}'      => 'Foo',
                '{value}'    => $fooVal,
            ]
        );

        $metadata = $createdServer->getMetadata();
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertArrayNotHasKey('Init', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testDeleteMetadata(Server $createdServer)
    {
        $createdServer->mergeMetadata(['Init' => $this->randomStr(), 'Init2' => $this->randomStr()]);

        require_once $this->sampleFile(
            'servers/delete_server_metadata_item.php',
            [
                '{serverId}' => $createdServer->id,
                '{key}'      => 'Init',
            ]
        );

        $metadata = $createdServer->getMetadata();
        $this->assertArrayNotHasKey('Init', $metadata);
        $this->assertArrayHasKey('Init2', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testStop(Server $createdServer)
    {
        require_once $this->sampleFile('servers/stop_server.php', ['{serverId}' => $createdServer->id]);

        $createdServer->waitUntil('SHUTOFF');
        $this->assertEquals('SHUTOFF', $createdServer->status);
    }

    /**
     * @depends testCreate
     * @depends testStop
     */
    public function testStart(Server $createdServer)
    {
        require_once $this->sampleFile('servers/start_server.php', ['{serverId}' => $createdServer->id]);

        $createdServer->waitUntil('ACTIVE');
        $this->assertEquals('ACTIVE', $createdServer->status);
    }

    /**
     * @depends testCreate
     */
    public function testRevertResize(Server $createdServer)
    {
        $flavorId = getenv('OS_RESIZE_FLAVOR');

        if (!$flavorId) {
            throw new RuntimeException('OS_RESIZE_FLAVOR env var must be set');
        }

        $createdServer->resize($flavorId);
        $createdServer->waitUntil('VERIFY_RESIZE');

        require_once $this->sampleFile('servers/revert_server_resize.php', ['{serverId}' => $createdServer->id]);

        $createdServer->waitUntil('ACTIVE');
        $this->assertEquals('ACTIVE', $createdServer->status);
        $this->assertEquals($createdServer->flavor->id, getenv('OS_FLAVOR'));
    }

    /**
     * @depends testCreate
     */
    public function testResize(Server $createdServer)
    {
        $flavorId = getenv('OS_RESIZE_FLAVOR');

        if (!$flavorId) {
            throw new RuntimeException('OS_RESIZE_FLAVOR env var must be set');
        }

        require_once $this->sampleFile(
            'servers/resize_server.php',
            [
                '{serverId}' => $createdServer->id,
                '{flavorId}' => $flavorId,
            ]
        );

        $createdServer->waitUntil('VERIFY_RESIZE');
        $this->assertEquals('VERIFY_RESIZE', $createdServer->status);
    }

    /**
     * @depends testCreate
     * @depends testResize
     */
    public function testConfirmResize(Server $createdServer)
    {
        require_once $this->sampleFile('servers/confirm_server_resize.php', ['{serverId}' => $createdServer->id]);

        $createdServer->waitUntil('ACTIVE');
        $this->assertEquals('ACTIVE', $createdServer->status);
        $this->assertEquals($createdServer->flavor->id, getenv('OS_RESIZE_FLAVOR'));
    }

    /**
     * @depends testCreate
     */
    public function testRebuild(Server $createdServer)
    {
        $newName = $this->randomStr();

        require_once $this->sampleFile(
            'servers/rebuild_server.php',
            [
                '{serverId}'   => $createdServer->id,
                '{imageId}'    => $this->searchImageId(),
                '{newName}'    => $newName,
                '{adminPass}'  => $this->randomStr(),
            ]
        );

        $createdServer->waitUntilActive(60);
        $this->assertEquals($newName, $createdServer->name);
    }

    /**
     * @depends testCreate
     */
    public function testRescue(Server $createdServer)
    {
        require_once $this->sampleFile(
            'servers/rescue_server.php',
            [
                '{serverId}'   => $createdServer->id,
                '{imageId}'    => $this->searchImageId(),
                '{adminPass}'  => $this->randomStr(),
            ]
        );

        $createdServer->waitUntil('RESCUE', 120);
        $this->assertEquals('RESCUE', $createdServer->status);
    }

    /**
     * @depends testCreate
     * @depends testRescue
     */
    public function testUnrescue(Server $createdServer)
    {
        require_once $this->sampleFile('servers/unrescue_server.php', ['{serverId}' => $createdServer->id]);

        $createdServer->waitUntil('ACTIVE', 120);
        $this->assertEquals('ACTIVE', $createdServer->status);
    }

    /**
     * @depends testCreate
     */
    public function testReboot(Server $createdServer)
    {
        require_once $this->sampleFile('servers/reboot_server.php', ['{serverId}' => $createdServer->id]);

        $createdServer->retrieve();
        $this->assertEquals('HARD_REBOOT', $createdServer->status);

        $createdServer->waitUntil('ACTIVE', 240);
        $this->assertEquals('ACTIVE', $createdServer->status);
    }

    /**
     * @depends testCreate
     */
    public function testGetVncConsole(Server $createdServer)
    {
        /** @var array $console */
        require_once $this->sampleFile('servers/get_server_vnc_console.php', [
            '{serverId}' => $createdServer->id,
        ]);

        $this->assertIsArray($console);
        $this->assertArrayHasKey('url', $console);
        $this->assertArrayHasKey('type', $console);
    }

    /**
     * @depends testCreate
     */
    public function testGetConsoleOutput(Server $createdServer)
    {
        // wait for the server to be ready
        sleep(5);

        /** @var string $consoleOutput */
        require_once $this->sampleFile('servers/get_server_console_output.php', ['{serverId}' => $createdServer->id]);

        $this->assertIsString($consoleOutput);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Server $createdServer)
    {
        require_once $this->sampleFile('servers/delete.php', ['{serverId}' => $createdServer->id]);

        // Needed so that subnet and network can be removed
        $createdServer->waitUntilDeleted();

        foreach ($this->getService()->listServers() as $server) {
            if ($server->id === $createdServer->id) {
                $this->fail('Server was not deleted');
            }
        }

        foreach (array_keys($createdServer->addresses) as $networkName) {
            $network = $this->getNetworkService()->listNetworks(['name' => $networkName])->current();
            $this->deleteNetwork($network);
        }

        $this->expectException(BadResponseError::class);
        $createdServer->retrieve();
    }

    public function testSuspend()
    {
        $server = $this->createServer();

        require_once $this->sampleFile('servers/suspend.php', ['{serverId}' => $server->id]);

        $server->waitUntil('SUSPENDED');
        $this->assertEquals('SUSPENDED', $server->status);

        // wait for the server to be fully suspended
        sleep(5);

        return $server;
    }

    /**
     * @depends testSuspend
     */
    public function testResume(Server $server)
    {
        $this->assertEquals('SUSPENDED', $server->status);

        require_once $this->sampleFile('servers/resume.php', ['{serverId}' => $server->id]);

        $server->waitUntil('ACTIVE', 300);
        $this->assertEquals('ACTIVE', $server->status);

        $this->deleteServer($server);
    }
}