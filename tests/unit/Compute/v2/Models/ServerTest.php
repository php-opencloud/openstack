<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\BlockStorage\v2\Models\VolumeAttachment;
use OpenStack\Networking\v2\Models\InterfaceAttachment;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Test\TestCase;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;

class ServerTest extends TestCase
{
    /** @var Server */
    private $server;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->server = new Server($this->client->reveal(), new Api());
        $this->server->id = 'serverId';
    }

    public function test_it_creates()
    {
        $opts = [
            'name'     => 'foo',
            'imageId'  => 'bar',
            'flavorId' => 'baz',
        ];

        $expectedJson = ['server' => [
            'name'      => $opts['name'],
            'imageRef'  => $opts['imageId'],
            'flavorRef' => $opts['flavorId'],
        ]];

        $this->setupMock('POST', 'servers', $expectedJson, [], 'server-post');
        $this->assertInstanceOf(Server::class, $this->server->create($opts));
    }

    public function test_it_creates_with_boot_from_volume()
    {
        $opts = [
            'name'     => 'foo',
            'flavorId' => 'baz',
            'blockDeviceMapping' => [['uuid' => 'aaaa-ddddd-bbbb-ccccc']]
        ];

        $expectedJson = ['server' => [
            'name' => $opts['name'],
            'flavorRef' => $opts['flavorId'],
            'block_device_mapping_v2' => $opts['blockDeviceMapping']
        ]];

        $this->setupMock('POST', 'servers', $expectedJson, [], 'server-post');
        $this->assertInstanceOf(Server::class, $this->server->create($opts));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage imageId or blockDeviceMapping.uuid must be set.
     */
    public function test_it_requires_image_id_or_volume_id_to_create_servers()
    {
        $this->server->create([
            'name' => 'some-server-name',
            'flavorId' => 'apple'
        ]);
    }

    public function test_it_updates()
    {
        // Updatable attributes
        $this->server->name = 'foo';
        $this->server->ipv4 = '0.0.0.0';
        $this->server->ipv6 = '0:0:0:0:0:ffff:0:0';

        $expectedJson = ['server' => [
            'name'       => 'foo',
            'accessIPv4' => '0.0.0.0',
            'accessIPv6' => '0:0:0:0:0:ffff:0:0',
        ]];

        $this->setupMock('PUT', 'servers/serverId', $expectedJson, [], 'server-put');

        $this->server->update();
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'servers/serverId', null, [], new Response(204));

        $this->assertNull($this->server->delete());
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'servers/serverId', null, [], 'server-get');

        $this->server->retrieve();

        $this->assertInstanceOf(Flavor::class, $this->server->flavor);
        $this->assertEquals("1", $this->server->flavor->id);
    }

    public function test_it_changes_password()
    {
        $expectedJson = ['changePassword' => ['adminPass' => 'foo']];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->changePassword('foo'));
    }

    public function test_it_reboots()
    {
        $expectedJson = ["reboot" => ["type" => "SOFT"]];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->reboot());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_an_exception_is_thrown_when_rebooting_with_an_invalid_type()
    {
        $this->server->reboot('foo');
    }

    public function test_it_rebuilds()
    {
        $userOptions = [
            'imageId'     => 'newImage',
            'name'        => 'newName',
            'metadata'    => [
                'foo' => 'bar',
                'baz' => 'bar',
            ],
            'personality' => [
                [
                    'path'     => '/etc/banner.txt',
                    'contents' => base64_encode('Hi there!'),
                ]
            ],
            'adminPass'   => 'foo',
        ];

        $expectedJson = json_encode(['rebuild' => [
            'imageRef'    => $userOptions['imageId'],
            'name'        => $userOptions['name'],
            'metadata'    => $userOptions['metadata'],
            'personality' => $userOptions['personality'],
            'adminPass'   => $userOptions['adminPass']
        ]], JSON_UNESCAPED_SLASHES);

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, ['Content-Type' => 'application/json'], 'server-rebuild');

        $this->server->rebuild($userOptions);

        $this->assertEquals($userOptions['imageId'], $this->server->image->id);
        $this->assertEquals($userOptions['name'], $this->server->name);
    }

    public function test_it_rescues()
    {
        $userOptions = [
            'imageId'     => 'newImage',
            'adminPass'   => 'foo',
        ];

        $expectedJson = [
            'rescue' => [
                'rescue_image_ref' => $userOptions['imageId'],
                'adminPass'        => $userOptions['adminPass']
            ]
        ];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-rescue');

        $adminPass = $this->server->rescue($userOptions);

        $this->assertEquals('foo', $adminPass);
    }

    public function test_it_unrescues()
    {
        $expectedJson = ['unrescue' => null];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-unrescue');

        $this->assertNull($this->server->unrescue());
    }

    public function test_it_starts()
    {
        $expectedJson = ['os-start' => null];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->start());
    }

    public function test_it_stops()
    {
        $expectedJson = ['os-stop' => null];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->stop());
    }

    public function test_it_resizes()
    {
        $expectedJson = ['resize' => ['flavorRef' => 'flavorId']];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->resize('flavorId'));
    }

    public function test_it_confirms_resizes()
    {
        $expectedJson = ['confirmResize' => null];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->confirmResize());
    }

    public function test_it_reverts_resizes()
    {
        $expectedJson = ['revertResize' => null];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->revertResize());
    }

    public function test_it_gets_console_output()
    {
        $expectedJson = ["os-getConsoleOutput" => ["length" => 3]];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-get-console-output');

        $this->assertEquals("FAKE CONSOLE OUTPUT\nANOTHER\nLAST LINE", $this->server->getConsoleOutput(3));
    }

    public function test_it_gets_all_console_output()
    {
        $expectedJson = ["os-getConsoleOutput" => new \stdClass()];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-get-console-output');

        $this->assertEquals("FAKE CONSOLE OUTPUT\nANOTHER\nLAST LINE", $this->server->getConsoleOutput());
    }

    public function test_it_gets_vnc_console()
    {
        $type = 'novnc';
        $expectedJson = ['os-getVNCConsole' => ['type' => $type]];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-get-console-vnc');

        $response = $this->server->getVncConsole();

        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals($type, $response['type']);
    }

    public function test_it_gets_rdp_console()
    {
        $type = 'rdp-html5';
        $expectedJson = ['os-getRDPConsole' => ['type' => $type]];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-get-console-rdp');

        $response = $this->server->getRDPConsole();

        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals($type, $response['type']);
    }

    public function test_it_gets_spice_console()
    {
        $type = 'spice-html5';
        $expectedJson = ['os-getSPICEConsole' => ['type' => $type]];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-get-console-spice');

        $response = $this->server->getSpiceConsole();

        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals($type, $response['type']);
    }

    public function test_it_gets_serial_console()
    {
        $type = 'serial';
        $expectedJson = ['os-getSerialConsole' => ['type' => $type]];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], 'server-get-console-serial');

        $response = $this->server->getSerialConsole();

        $this->assertArrayHasKey('url', $response);
        $this->assertArrayHasKey('type', $response);
        $this->assertEquals($type, $response['type']);
    }

    public function test_it_creates_images()
    {
        $userData = ['name' => 'newImage', 'metadata' => ['foo' => 'bar']];

        $expectedJson = ['createImage' => $userData];
        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->assertNull($this->server->createImage($userData));
    }

    public function test_it_gets_ip_addresses()
    {
        $this->setupMock('GET', 'servers/serverId/ips', null, [], 'server-ips');

        $ips = $this->server->listAddresses();

        $this->assertInternalType('array', $ips);
        $this->assertCount(4, $ips['public']);
        $this->assertCount(2, $ips['private']);
    }

    public function test_it_gets_ip_addresses_by_network_label()
    {
        $this->setupMock('GET', 'servers/serverId/ips/foo', null, [], 'server-ips');

        $ips = $this->server->listAddresses(['networkLabel' => 'foo']);

        $this->assertInternalType('array', $ips);
        $this->assertCount(4, $ips['public']);
        $this->assertCount(2, $ips['private']);
    }

    public function test_it_retrieves_metadata()
    {
        $this->setupMock('GET', 'servers/serverId/metadata', null, [], 'server-metadata-get');

        $metadata = $this->server->getMetadata();

        $this->assertEquals('x86_64', $metadata['architecture']);
        $this->assertEquals('True', $metadata['auto_disk_config']);
        $this->assertEquals('nokernel', $metadata['kernel_id']);
        $this->assertEquals('nokernel', $metadata['ramdisk_id']);
    }

    public function test_it_sets_metadata()
    {
        $metadata = ['foo' => '1', 'bar' => '2'];

        $expectedJson = ['metadata' => $metadata];
        $response = $this->createResponse(200, [], $expectedJson);
        $this->setupMock('PUT', 'servers/serverId/metadata', $expectedJson, [], $response);

        $this->server->resetMetadata($metadata);

        $this->assertEquals('1', $this->server->metadata['foo']);
    }

    public function test_it_updates_metadata()
    {
        $metadata = ['foo' => '1'];

        $expectedJson = ['metadata' => $metadata];
        $response = $this->createResponse(200, [], array_merge_recursive($expectedJson, ['metadata' => ['bar' => '2']]));
        $this->setupMock('POST', 'servers/serverId/metadata', $expectedJson, [], $response);

        $this->server->mergeMetadata($metadata);

        $this->assertEquals('1', $this->server->metadata['foo']);
        $this->assertEquals('2', $this->server->metadata['bar']);
    }

    public function test_it_retrieves_a_metadata_item()
    {
        $response = $this->createResponse(200, [], ['metadata' => ['fooKey' => 'bar']]);
        $this->setupMock('GET', 'servers/serverId/metadata/fooKey', null, [], $response);

        $value = $this->server->getMetadataItem('fooKey');

        $this->assertEquals('bar', $value);
    }

    public function test_it_deletes_a_metadata_item()
    {
        $this->setupMock('DELETE', 'servers/serverId/metadata/fooKey', null, [], new Response(204));

        $this->assertNull($this->server->deleteMetadataItem('fooKey'));
    }

    public function test_it_lists_security_groups()
    {
        $this->setupMock('GET', 'servers/serverId/os-security-groups', null, [], 'server-security-groups-get');

        $securityGroups = iterator_to_array($this->server->listSecurityGroups());

        $this->assertInstanceOf(SecurityGroup::class, $securityGroups[0]);
    }

    public function test_it_lists_volume_attachments()
    {
        $this->setupMock('GET', 'servers/serverId/os-volume_attachments', null, [], 'server-volume-attachments-get');

        $volumeAttachments = iterator_to_array($this->server->listVolumeAttachments());

        $this->assertInstanceOf(VolumeAttachment::class, $volumeAttachments[0]);
    }

    public function test_it_remove_security_group()
    {
        $opt = [
            'name' => 'secgroup_to_remove'
        ];

        $expectedJson = [
            'removeSecurityGroup' => $opt
        ];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->server->removeSecurityGroup($opt);
    }

    public function test_it_add_security_group()
    {
        $opt = [
            'name' => 'secgroup_to_add'
        ];

        $expectedJson = [
            'addSecurityGroup' => $opt
        ];

        $this->setupMock('POST', 'servers/serverId/action', $expectedJson, [], new Response(202));

        $this->server->addSecurityGroup($opt);
    }

    public function test_it_attaches_volume()
    {
        $volumeId = 'fooooobarrrr';

        $expectedJson = [
            'volumeAttachment' => ['volumeId' => $volumeId]
        ];

        $this->setupMock('POST', 'servers/serverId/os-volume_attachments', $expectedJson, [], 'server-volume-attach-post');

        $volumeAttachment = $this->server->attachVolume($volumeId);
        $this->assertInstanceOf(VolumeAttachment::class, $volumeAttachment);
        $this->assertEquals('serverId', $volumeAttachment->serverId);
        $this->assertEquals('a26887c6-c47b-4654-abb5-dfadf7d3f803', $volumeAttachment->id);
        $this->assertEquals($volumeId, $volumeAttachment->volumeId);
        $this->assertEquals('/dev/vdd', $volumeAttachment->device);
    }

    public function test_it_detaches_volume()
    {
        $attachmentId = 'a-dummy-attachment-id';

        $this->setupMock('DELETE', 'servers/serverId/os-volume_attachments/' . $attachmentId, null, [], new Response(202));

        $this->server->detachVolume($attachmentId);
    }

    public function test_it_lists_interface_attachments()
    {
        $this->setupMock('GET', 'servers/serverId/os-interface', null, [], 'server-interface-attachments-get');

        $interfaceAttachments = iterator_to_array($this->server->listInterfaceAttachments());

        $this->assertEquals('ce531f90-199f-48c0-816c-13e38010b442', $interfaceAttachments[0]->portId);
        $this->assertEquals('ACTIVE', $interfaceAttachments[0]->portState);
        $this->assertEquals('3cb9bc59-5699-4588-a4b1-b87f96708bc6', $interfaceAttachments[0]->netId);
        $this->assertEquals('fa:16:3e:4c:2c:30', $interfaceAttachments[0]->macAddr);
        $this->assertEquals('192.168.1.3', $interfaceAttachments[0]->fixedIps[0]['ip_address']);
        $this->assertEquals('f8a6e8f8-c2ec-497c-9f23-da9616de54ef', $interfaceAttachments[0]->fixedIps[0]['subnet_id']);

        $this->assertInstanceOf(InterfaceAttachment::class, $interfaceAttachments[0]);
    }

    /** @test */
    public function it_gets_interface_attachments()
    {
        $portId = 'fooooobarrrr';

        $this->setupMock('GET', 'servers/serverId/os-interface/' . $portId, ['port_id' => 'fooooobarrrr'], [], 'server-interface-attachment-get');

        $interfaceAttachment = $this->server->getInterfaceAttachment($portId);

        $this->assertEquals('ce531f90-199f-48c0-816c-13e38010b442', $interfaceAttachment->portId);
        $this->assertEquals('ACTIVE', $interfaceAttachment->portState);
        $this->assertEquals('3cb9bc59-5699-4588-a4b1-b87f96708bc6', $interfaceAttachment->netId);
        $this->assertEquals('fa:16:3e:4c:2c:30', $interfaceAttachment->macAddr);
        $this->assertEquals('192.168.1.3', $interfaceAttachment->fixedIps[0]['ip_address']);
        $this->assertEquals('f8a6e8f8-c2ec-497c-9f23-da9616de54ef', $interfaceAttachment->fixedIps[0]['subnet_id']);
    }

    /** @test */
    public function test_it_creates_interface_attachments()
    {
        $networkId = 'fooooobarrrr';

        $expectedJson = [
            'interfaceAttachment' => ['net_id' => $networkId]
        ];

        $this->setupMock('POST', 'servers/serverId/os-interface', $expectedJson, [], 'server-interface-attachments-post');

        $interfaceAttachment = $this->server->createInterfaceAttachment(['networkId' => $networkId]);

        $this->assertEquals('ACTIVE', $interfaceAttachment->portState);
        $this->assertEquals('10.0.0.1', $interfaceAttachment->fixedIps[0]['ip_address']);
    }

    public function test_it_detaches_interfaces()
    {
        $portId = 'a-dummy-port-id';

        $this->setupMock('DELETE', 'servers/serverId/os-interface/' . $portId, ['port_id' => $portId], [], new Response(202));

        $this->server->detachInterface($portId);
    }
}
