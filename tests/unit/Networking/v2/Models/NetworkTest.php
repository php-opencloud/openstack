<?php

namespace OpenStack\Test\Networking\v2\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\Network;
use OpenStack\Test\TestCase;

class NetworkTest extends TestCase
{
    private $network;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->network = new Network($this->client->reveal(), new Api());
        $this->network->id = 'networkId';
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'v2.0/networks/networkId');
        $this->setupMockResponse($request, 'network-get');

        $this->network->retrieve();

        $this->assertEquals('fakenetwork', $this->network->name);
    }
}
