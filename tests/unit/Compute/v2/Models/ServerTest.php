<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\Server;
use OpenCloud\Test\TestCase;
use Prophecy\Argument;

class ServerTest extends TestCase
{
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
}
