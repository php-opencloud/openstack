<?php

namespace OpenStack\Test\Networking\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\LoadBalancerStatus;
use OpenStack\Networking\v2\Models\LoadBalancerListener;
use OpenStack\Test\TestCase;

class LoadBalancerStatusTest extends TestCase
{
    private $status;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->status = new LoadBalancerStatus($this->client->reveal(), new Api());
        $this->status->loadbalancerId = 'loadbalancerId';
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'v2.0/lbaas/loadbalancers/loadbalancerId/statuses', null, [], 'loadbalancer-statuses-get');

        $this->status->retrieve();

        $this->assertEquals('loadbalancer1', $this->status->name);
        $this->assertEquals('loadbalancerId', $this->status->id);
        $this->assertEquals('ONLINE', $this->status->operatingStatus);
        $this->assertEquals('ACTIVE', $this->status->provisioningStatus);
        $this->assertInternalType('array', $this->status->listeners);
        $this->assertArrayHasKey(0, $this->status->listeners);
        $this->assertInstanceOf(LoadBalancerListener::class, $this->status->listeners[0]);
    }
}
