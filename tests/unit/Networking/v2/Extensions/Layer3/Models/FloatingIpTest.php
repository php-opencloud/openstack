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
    }
}
