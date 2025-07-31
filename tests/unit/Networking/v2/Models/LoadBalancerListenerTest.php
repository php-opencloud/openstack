<?php

namespace OpenStack\Test\Networking\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\LoadBalancerListener;
use OpenStack\Test\TestCase;

class LoadBalancerListenerTest extends TestCase
{
    private $listener;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->listener = new LoadBalancerListener($this->client->reveal(), new Api());
        $this->listener->id = 'listenerId';
    }

    public function test_it_creates()
    {
        $opts = [
            'name'            => 'listener1',
            'description'     => 'simple listener',
            'tenantId'        => 'b7c1a69e88bf4b21a8148f787aef2081',
            'protocol'        => 'HTTP',
            'protocolPort'    => 443,
            'connectionLimit' => 1000,
            'adminStateUp'    => true
        ];

        $expectedJson = ['listener' => [
            'name'             => $opts['name'],
            'description'      => $opts['description'],
            'tenant_id'        => $opts['tenantId'],
            'protocol'         => $opts['protocol'],
            'protocol_port'    => $opts['protocolPort'],
            'admin_state_up'   => $opts['adminStateUp'],
            'connection_limit' => $opts['connectionLimit']
        ]];

        $this->mockRequest('POST', 'v2.0/lbaas/listeners', 'loadbalancer-listener-post', $expectedJson, []);

        self::assertInstanceOf(LoadBalancerListener::class, $this->listener->create($opts));
    }

    public function test_it_updates()
    {
        // Updatable attributes
        $this->listener->name = 'foo';
        $this->listener->description = 'bar';
        $this->listener->connectionLimit = 999;
        $this->listener->adminStateUp = false;

        $expectedJson = ['listener' => [
            'name'             => 'foo',
            'description'      => 'bar',
            'connection_limit' => 999,
            'admin_state_up'   => false
        ]];

        $this->mockRequest('PUT', 'v2.0/lbaas/listeners/listenerId', 'loadbalancer-listener-put', $expectedJson, []);

        $this->listener->update();
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'v2.0/lbaas/listeners/listenerId', 'loadbalancer-listener-get', null, []);

        $this->listener->retrieve();

        self::assertEquals('listenerId', $this->listener->id);
        self::assertEquals('listener1', $this->listener->name);
        self::assertEquals('simple listener', $this->listener->description);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'v2.0/lbaas/listeners/listenerId', new Response(204), null, []);

        $this->listener->delete();
    }
}
