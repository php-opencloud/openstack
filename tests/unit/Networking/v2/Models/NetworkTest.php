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

    public function test_it_creates()
    {
        $opts = [
            'name' => 'foo',
            'shared' => false,
            'admin_state_up' => true
        ];

        $expectedJson = ['network' => [
            'name' => $opts['name'],
            'shared' => $opts['shared'],
            'admin_state_up' => $opts['admin_state_up'],
        ]];

        $req = $this->setupMockRequest('POST', 'v2.0/networks', $expectedJson);
        $this->setupMockResponse($req, 'network-post');

        $this->assertInstanceOf(Network::class, $this->network->create($opts));
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'v2.0/networks/networkId');
        $this->setupMockResponse($request, 'network-get');

        $this->network->retrieve();

        $this->assertEquals('networkId', $this->network->id);
        $this->assertEquals('fakenetwork', $this->network->name);
        $this->assertEquals('ACTIVE', $this->network->status);
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'v2.0/networks/networkId');
        $this->setupMockResponse($request, new Response(204));

        $this->network->delete();
    }
}
