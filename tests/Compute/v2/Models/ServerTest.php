<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
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

        $this->server = new Server($this->client->reveal());
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

        $request = new Request('POST', 'servers');

        $this->client
            ->createRequest('POST', 'servers', ['json' => $expectedJson])
            ->shouldBeCalled()
            ->willReturn($request);

        $this->client
            ->send(Argument::is($request))
            ->shouldBeCalled()
            ->willReturn($this->getFixture('server-post'));

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

        $request = new Request('PUT', 'server/serverId');

        $this->client
            ->createRequest('PUT', 'servers/serverId', ['json' => $expectedJson])
            ->shouldBeCalled()
            ->willReturn($request);

        $this->client
            ->send(Argument::is($request))
            ->shouldBeCalled()
            ->willReturn($this->getFixture('server-put'));

        $this->assertInstanceOf(Server::class, $this->server->update());
    }

    public function test_it_deletes()
    {
        $req = new Request('DELETE', '');

        $this->client
            ->createRequest('DELETE', 'servers/serverId', [])
            ->shouldBeCalled()
            ->willReturn($req);

        $this->client
            ->send(Argument::is($req))
            ->shouldBeCalled()
            ->willReturn(new Response(204));

        $this->assertNull($this->server->delete());
    }

    public function test_it_retrieves()
    {
        $request = new Request('GET', 'server/foo');

        $this->client
            ->createRequest('GET', 'servers/foo', [])
            ->shouldBeCalled()
            ->willReturn($request);

        $this->client
            ->send(Argument::is($request))
            ->shouldBeCalled()
            ->willReturn($this->getFixture('server-get'));

        $this->server->id = 'foo';

        $this->assertInstanceOf(Server::class, $this->server->retrieve());

        $this->assertInstanceOf(Flavor::class, $this->server->flavor);
        $this->assertEquals("1", $this->server->flavor->id);
    }

    public function test_it_changes_password()
    {

    }

    public function test_it_reboots()
    {

    }

    public function test_it_rebuilds()
    {

    }

    public function test_it_resizes()
    {

    }

    public function test_it_confirms_resizes()
    {

    }

    public function test_it_reverts_resizes()
    {

    }

    public function test_it_creates_images()
    {

    }

    public function test_it_gets_ip_addresses()
    {

    }

    public function test_it_gets_ip_addresses_by_network_label()
    {

    }

    public function test_it_retrieves_metadata()
    {

    }

    public function test_it_sets_metadata()
    {

    }

    public function test_it_updates_metadata()
    {

    }

    public function test_it_retrieves_a_metadata_item()
    {

    }

    public function test_it_deletes_a_metadata_item()
    {

    }
}
