<?php

namespace OpenStack\Test\Networking\v2;

use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Service;
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

    public function test_it_gets_an_network()
    {
      $network = $this->service->getNetwork([
            'id' => 'networkId'
        ]);

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals('networkId', $network->id);
    }
}
