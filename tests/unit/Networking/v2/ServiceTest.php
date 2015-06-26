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

    public function test_it_creates_an_network()
    {
        $opts = [
            'name' => 'foo',
            'shared' => false,
            'adminStateUp' => true
        ];

        $expectedJson = ['network' => [
            'name' => $opts['name'],
            'shared' => $opts['shared'],
            'admin_state_up' => $opts['adminStateUp'],
        ]];

        $req = $this->setupMockRequest('POST', 'v2.0/networks', $expectedJson);
        $this->setupMockResponse($req, 'network-post');

        $this->assertInstanceOf(Network::class, $this->service->createNetwork($opts));
    }

    public function test_it_bulk_creates_networks()
    {
        $opts = [
            [
                'name' => 'foo',
                'shared' => false,
                'adminStateUp' => true
            ],
            [
                'name' => 'bar',
                'shared' => true,
                'adminStateUp' => false
            ],
        ];

        $expectedJson = [
            'networks' => [
                [
                    'name' => $opts[0]['name'],
                    'shared' => $opts[0]['shared'],
                    'admin_state_up' => $opts[0]['adminStateUp']
                ],
                [
                    'name' => $opts[1]['name'],
                    'shared' => $opts[1]['shared'],
                    'admin_state_up' => $opts[1]['adminStateUp']
                ],
            ],
        ];

        $req = $this->setupMockRequest('POST', 'v2.0/networks', $expectedJson);
        $this->setupMockResponse($req, 'networks-post');

        $networks = $this->service->createNetworks($opts);

        $this->assertInternalType('array', $networks);
        $this->assertCount(2, $networks);
    }

    public function test_it_gets_an_network()
    {
      $network = $this->service->getNetwork('networkId');

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals('networkId', $network->id);
    }
}
