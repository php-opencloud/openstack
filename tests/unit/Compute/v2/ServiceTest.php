<?php

namespace OpenStack\Test\Compute\v2;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Compute\v2\Service;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_creates_servers()
    {
        $opts = [
            'name' => 'foo',
            'imageId' => '',
            'flavorId' => '',
        ];

        $expectedJson = ['server' => [
            'name' => $opts['name'],
            'imageRef' => $opts['imageId'],
            'flavorRef' => $opts['flavorId'],
        ]];

        $this->setupMock('POST', 'servers', $expectedJson, [], 'server-post');

        $this->assertInstanceOf(Server::class, $this->service->createServer($opts));
    }

    public function test_it_lists_servers()
    {
        $this->client
            ->request('GET', 'servers', ['query' => ['limit' => 5], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('servers-get'));

        $this->client
            ->request('GET', 'servers', ['query' => ['limit' => 5, 'marker' => '5'], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn(new Response(204));

        foreach ($this->service->listServers(false, ['limit' => 5]) as $server) {
            $this->assertInstanceOf(Server::class, $server);
        }
    }

    public function test_it_gets_a_server()
    {
        $server = $this->service->getServer([
            'id' => 'serverId'
        ]);

        $this->assertInstanceOf(Server::class, $server);
        $this->assertEquals('serverId', $server->id);
    }

    public function test_it_lists_flavors()
    {
        $this->client
            ->request('GET', 'flavors', ['query' => ['limit' => 5], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('flavors-get'));

        $this->client
            ->request('GET', 'flavors', ['query' => ['limit' => 5, 'marker' => '5'], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn(new Response(204));

        $count = 0;

        foreach ($this->service->listFlavors(['limit' => 5]) as $flavor) {
            ++$count;
            $this->assertInstanceOf(Flavor::class, $flavor);
        }

        $this->assertEquals(5, $count);
    }

    public function test_it_gets_a_flavor()
    {
        $flavor = $this->service->getFlavor([
            'id' => 'flavorId'
        ]);

        $this->assertInstanceOf(Flavor::class, $flavor);
        $this->assertEquals('flavorId', $flavor->id);
    }

    public function test_it_lists_images()
    {
        $this->client
            ->request('GET', 'images', ['query' => ['limit' => 5], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('images-get'));

        $this->client
            ->request('GET', 'images', ['query' => ['limit' => 5, 'marker' => '76fa36fc-c930-4bf3-8c8a-ea2a2420deb6'], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn(new Response(204));

        foreach ($this->service->listImages(['limit' => 5]) as $image) {
            $this->assertInstanceOf(Image::class, $image);
        }
    }

    public function test_it_gets_an_image()
    {
        $image = $this->service->getImage([
            'id' => 'imageId'
        ]);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('imageId', $image->id);
    }
}