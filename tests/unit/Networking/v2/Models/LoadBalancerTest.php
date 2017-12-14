<?php

namespace OpenStack\Test\Networking\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\LoadBalancer;
use OpenStack\Networking\v2\Models\LoadBalancerListener;
use OpenStack\Networking\v2\Models\LoadBalancerStat;
use OpenStack\Networking\v2\Models\LoadBalancerStatus;
use OpenStack\Test\TestCase;

class LoadBalancerTest extends TestCase
{
    private $loadbalancer;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->loadbalancer = new LoadBalancer($this->client->reveal(), new Api());
        $this->loadbalancer->id = 'loadbalancerId';
    }

    public function test_it_creates()
    {
        $opts = [
            'name'         => 'loadbalancer1',
            'description'  => 'simple lb',
            'tenantId'     => 'b7c1a69e88bf4b21a8148f787aef2081',
            'vipSubnetId'  => '013d3059-87a4-45a5-91e9-d721068ae0b2',
            'vipAddress'   => '10.0.0.4',
            'adminStateUp' => true
        ];

        $expectedJson = ['loadbalancer' => [
            'name'           => $opts['name'],
            'description'    => $opts['description'],
            'tenant_id'      => $opts['tenantId'],
            'vip_subnet_id'  => $opts['vipSubnetId'],
            'vip_address'    => $opts['vipAddress'],
            'admin_state_up' => $opts['adminStateUp']
        ]];

        $this->setupMock('POST', 'v2.0/lbaas/loadbalancers', $expectedJson, [], 'loadbalancer-post');

        $this->assertInstanceOf(LoadBalancer::class, $this->loadbalancer->create($opts));
    }

    public function test_it_updates()
    {
        // Updatable attributes
        $this->loadbalancer->name = 'foo';
        $this->loadbalancer->description = 'bar';

        $expectedJson = ['loadbalancer' => [
            'name'        => 'foo',
            'description' => 'bar'
        ]];

        $this->setupMock('PUT', 'v2.0/lbaas/loadbalancers/loadbalancerId', $expectedJson, [], 'loadbalancer-put');

        $this->loadbalancer->update();
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'v2.0/lbaas/loadbalancers/loadbalancerId', null, [], 'loadbalancer-get');

        $this->loadbalancer->retrieve();

        $this->assertEquals('loadbalancerId', $this->loadbalancer->id);
        $this->assertEquals('loadbalancer1', $this->loadbalancer->name);
        $this->assertEquals('simple lb', $this->loadbalancer->description);
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'v2.0/lbaas/loadbalancers/loadbalancerId', null, [], new Response(204));

        $this->loadbalancer->delete();
    }

    public function test_add_listener()
    {
        $opts = [
            'name'            => 'listener1',
            'description'     => 'simple listener',
            'tenantId'        => 'tenantId',
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
            'connection_limit' => $opts['connectionLimit'],
            'admin_state_up'   => $opts['adminStateUp'],
            'loadbalancer_id'  => 'loadbalancerId'
        ]];

        $this->setupMock('POST', 'v2.0/lbaas/listeners', $expectedJson, [], 'loadbalancer-listener-post');

        $this->assertInstanceOf(LoadBalancerListener::class, $this->loadbalancer->addListener($opts));
    }

    public function test_get_stats()
    {
        $this->setupMock('GET', 'v2.0/lbaas/loadbalancers/loadbalancerId/stats', null, [], 'loadbalancer-stats-get');

        $stats = $this->loadbalancer->getStats();

        $this->assertEquals('4321', $stats->bytesIn);
        $this->assertEquals('1234', $stats->bytesOut);
        $this->assertEquals(25, $stats->totalConnections);
        $this->assertEquals(10, $stats->activeConnections);
        $this->assertEquals($this->loadbalancer->id, $stats->loadbalancerId);
        $this->assertInstanceOf(LoadBalancerStat::class, $stats);
    }

    public function test_get_statuses()
    {
        $this->setupMock('GET', 'v2.0/lbaas/loadbalancers/loadbalancerId/statuses', null, [], 'loadbalancer-statuses-get');

        $status = $this->loadbalancer->getStatuses();
        $this->assertEquals('loadbalancer1', $status->name);
        $this->assertEquals('loadbalancerId', $status->id);
        $this->assertEquals('ONLINE', $status->operatingStatus);
        $this->assertEquals('ACTIVE', $status->provisioningStatus);
        $this->assertInternalType('array', $status->listeners);
        $this->assertArrayHasKey(0, $status->listeners);
        $this->assertInstanceOf(LoadBalancerListener::class, $status->listeners[0]);
        $this->assertInstanceOf(LoadBalancerStatus::class, $status);
    }
}
