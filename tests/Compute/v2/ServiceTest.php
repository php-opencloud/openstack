<?php

namespace OpenStack\Test\Compute\v2;

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

        $this->service = new Service($this->client->reveal());
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

        $req = $this->setupMockRequest('POST', 'servers', $expectedJson);
        $this->setupMockResponse($req, 'server-post');

        $this->assertInstanceOf(Server::class, $this->service->createServer($opts));
    }
} 