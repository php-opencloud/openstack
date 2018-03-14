<?php

namespace OpenStack\Test\Networking\v2\Extensions\Layer3\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Test\TestCase;
use OpenStack\Networking\v2\Extensions\Layer3\Api;
use OpenStack\Networking\v2\Extensions\Layer3\Models\FloatingIp;

class FloatingIpTest extends TestCase
{
    /** @var FloatingIp */
    private $floatingIp;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->floatingIp = new FloatingIp($this->client->reveal(), new Api());
        $this->floatingIp->id = 'id';
    }

    public function test_it_updates()
    {
        $expectedJson = ['floatingip' => [
            "floating_network_id" => "376da547-b977-4cfe-9cba-275c80debf57",
            "port_id"             => "ce705c24-c1ef-408a-bda3-7bbd946164ab",
        ]];

        $this->setupMock('PUT', 'v2.0/floatingips/id', $expectedJson, [], new Response(202));

        $this->floatingIp->floatingNetworkId = "376da547-b977-4cfe-9cba-275c80debf57";
        $this->floatingIp->portId = "ce705c24-c1ef-408a-bda3-7bbd946164ab";
        $this->floatingIp->update();
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'v2.0/floatingips/id', null, [], new Response(202));

        $this->floatingIp->delete();
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'v2.0/floatingips/id', null, [], 'FloatingIp');

        $this->floatingIp->retrieve();

        $this->assertEquals(
            '376da547-b977-4cfe-9cba-275c80debf57',
                            $this->floatingIp->floatingNetworkId
        );
        $this->assertEquals(
            'd23abc8d-2991-4a55-ba98-2aaea84cc72f',
                            $this->floatingIp->routerId
        );
        $this->assertEquals(
            '10.0.0.3',
                            $this->floatingIp->fixedIpAddress
        );
        $this->assertEquals(
            '172.24.4.228',
                            $this->floatingIp->floatingIpAddress
        );
        $this->assertEquals(
            '4969c491a3c74ee4af974e6d800c62de',
                            $this->floatingIp->tenantId
        );
        $this->assertEquals('ACTIVE', $this->floatingIp->status);
        $this->assertEquals(
            'ce705c24-c1ef-408a-bda3-7bbd946164ab',
                            $this->floatingIp->portId
        );
        $this->assertEquals(
            '2f245a7b-796b-4f26-9cf9-9e82d248fda7',
                            $this->floatingIp->id
        );
    }
}
