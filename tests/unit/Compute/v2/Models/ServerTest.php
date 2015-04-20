<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Test\TestCase;
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
            'name' => 'foo',
            'imageId' => 'bar',
            'flavorId' => 'baz',
        ];

        $expectedJson = ['server' => [
            'name' => $opts['name'],
            'imageRef' => $opts['imageId'],
            'flavorRef' => $opts['flavorId'],
        ]];

        $req = $this->setupMockRequest('POST', 'servers', $expectedJson);
        $this->setupMockResponse($req, 'server-post');

        $this->assertInstanceOf(Server::class, $this->server->create($opts));
    }

    public function test_it_updates()
    {
        // Updatable attributes
        $this->server->name = 'foo';
        $this->server->ipv4 = '0.0.0.0';
        $this->server->ipv6 = '0:0:0:0:0:ffff:0:0';

        $expectedJson = ['server' => [
            'name' => 'foo',
            'accessIPv4' => '0.0.0.0',
            'accessIPv6' => '0:0:0:0:0:ffff:0:0',
        ]];

        $request = $this->setupMockRequest('PUT', 'servers/serverId', $expectedJson);
        $this->setupMockResponse($request, 'server-put');

        $this->assertInstanceOf(Server::class, $this->server->update());
    }

    public function test_it_deletes()
    {
        $req = $this->setupMockRequest('DELETE', 'servers/serverId', []);
        $this->setupMockResponse($req, new Response(204));

        $this->assertNull($this->server->delete());
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'servers/serverId');
        $this->setupMockResponse($request, 'server-get');

        $this->assertInstanceOf(Server::class, $this->server->retrieve());
        $this->assertInstanceOf(Flavor::class, $this->server->flavor);
        $this->assertEquals("1", $this->server->flavor->id);
    }

    public function test_it_changes_password()
    {
        $expectedJson = ['changePassword' => ['adminPass' => 'foo']];
        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, new Response(202));

        $this->assertNull($this->server->changePassword('foo'));
    }

    public function test_it_reboots()
    {
        $expectedJson = ["reboot" => ["type" => "SOFT"]];
        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, new Response(202));

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
            'imageId' => 'newImage',
            'name'    => 'newName',
            'metadata' => [
                'foo' => 'bar',
                'baz' => 'bar',
            ],
            'personality' => [
                [
                    'path'     => '/etc/banner.txt',
                    'contents' => base64_encode('Hi there!'),
                ]
            ]
        ];

        $expectedJson = ['rebuild' => [
            'imageRef' => $userOptions['imageId'],
            'name'     => $userOptions['name'],
            'metadata' => $userOptions['metadata'],
            'personality' => $userOptions['personality'],
        ]];

        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, 'server-rebuild');

        $this->server->rebuild($userOptions);

        $this->assertEquals($userOptions['imageId'], $this->server->image->id);
        $this->assertEquals($userOptions['name'], $this->server->name);
    }

    public function test_it_resizes()
    {
        $expectedJson = ['resize' => ['flavorRef' => 'flavorId']];

        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, new Response(202));

        $this->assertNull($this->server->resize('flavorId'));
    }

    public function test_it_confirms_resizes()
    {
        $expectedJson = ['confirmResize' => null];

        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, new Response(202));

        $this->assertNull($this->server->confirmResize());
    }

    public function test_it_reverts_resizes()
    {
        $expectedJson = ['revertResize' => null];

        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, new Response(202));

        $this->assertNull($this->server->revertResize());
    }

    public function test_it_creates_images()
    {
        $userData = ['name' => 'newImage', 'metadata' => ['foo' => 'bar']];

        $expectedJson = ['createImage' => $userData];

        $request = $this->setupMockRequest('POST', 'servers/serverId/action', $expectedJson);
        $this->setupMockResponse($request, new Response(202));

        $this->assertNull($this->server->createImage($userData));
    }

    public function test_it_gets_ip_addresses()
    {
        $request = $this->setupMockRequest('GET', 'servers/serverId/ips');
        $this->setupMockResponse($request, 'server-ips');

        $ips = $this->server->listAddresses();

        $this->assertInternalType('array', $ips);
        $this->assertCount(4, $ips['public']);
        $this->assertCount(2, $ips['private']);
    }

    public function test_it_gets_ip_addresses_by_network_label()
    {
        $request = $this->setupMockRequest('GET', 'servers/serverId/ips/foo');
        $this->setupMockResponse($request, 'server-ips');

        $ips = $this->server->listAddresses(['networkLabel' => 'foo']);

        $this->assertInternalType('array', $ips);
        $this->assertCount(4, $ips['public']);
        $this->assertCount(2, $ips['private']);
    }

    public function test_it_retrieves_metadata()
    {
        $request = $this->setupMockRequest('GET', 'servers/serverId/metadata');
        $this->setupMockResponse($request, 'server-metadata-get');

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

        $request = $this->setupMockRequest('PUT', 'servers/serverId/metadata', $expectedJson);
        $this->setupMockResponse($request, $this->createResponse(200, [], $expectedJson));

        $metadata = $this->server->resetMetadata($metadata);

        $this->assertEquals('1', $metadata['foo']);
    }

    public function test_it_updates_metadata()
    {
        $metadata = ['foo' => '1'];

        $expectedJson = ['metadata' => $metadata];

        $request = $this->setupMockRequest('POST', 'servers/serverId/metadata', $expectedJson);
        $this->setupMockResponse($request, $this->createResponse(200, [], array_merge_recursive($expectedJson, ['metadata' => ['bar' => '2']])));

        $metadata = $this->server->mergeMetadata($metadata);

        $this->assertEquals('1', $metadata['foo']);
        $this->assertEquals('2', $metadata['bar']);
    }

    public function test_it_retrieves_a_metadata_item()
    {
        $request = $this->setupMockRequest('GET', 'servers/serverId/metadata/fooKey');
        $this->setupMockResponse($request, $this->createResponse(200, [], ['metadata' => ['fooKey' => 'bar']]));

        $value = $this->server->getMetadataItem('fooKey');

        $this->assertEquals('bar', $value);
    }

    public function test_it_deletes_a_metadata_item()
    {
        $request = $this->setupMockRequest('DELETE', 'servers/serverId/metadata/fooKey');
        $this->setupMockResponse($request, new Response(204));

        $this->assertNull($this->server->deleteMetadataItem('fooKey'));
    }
}