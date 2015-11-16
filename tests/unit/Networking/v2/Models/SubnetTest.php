<?php

namespace OpenStack\Test\Subneting\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\Subnet;
use OpenStack\Test\TestCase;

class SubnetTest extends TestCase
{
    private $subnet;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->subnet = new Subnet($this->client->reveal(), new Api());
        $this->subnet->id = 'subnetId';
    }

    public function test_it_creates()
    {
        $opts = [
            'name' => 'foo',
            'networkId' => 'networkId',
            'tenantId' => 'tenantId',
            'ipVersion' => 4,
            'cidr' => '192.168.199.0/24',
            'enableDhcp' => false,
        ];

        $expectedJson = ['subnet' => [
            'name' => $opts['name'],
            'network_id' => $opts['networkId'],
            'tenant_id' => $opts['tenantId'],
            'ip_version' => $opts['ipVersion'],
            'cidr' => $opts['cidr'],
            'enable_dhcp' => $opts['enableDhcp'],
        ]];

        $this->setupMock('POST', 'v2.0/subnets', $expectedJson, [], 'subnet-post');

        $this->assertInstanceOf(Subnet::class, $this->subnet->create($opts));
    }

    public function test_it_bulk_creates()
    {
        $opts = [
            [
                'name' => 'foo',
                'networkId' => 'networkId',
                'tenantId' => 'tenantId',
                'ipVersion' => 4,
                'cidr' => '192.168.199.0/24',
            ],
            [
                'name' => 'bar',
                'networkId' => 'networkId',
                'tenantId' => 'tenantId',
                'ipVersion' => 4,
                'cidr' => '10.56.4.0/22',
            ],
        ];

        $expectedJson = [
            'subnets' => [
                [
                    'name' => $opts[0]['name'],
                    'network_id' => $opts[0]['networkId'],
                    'tenant_id' => $opts[0]['tenantId'],
                    'ip_version' => $opts[0]['ipVersion'],
                    'cidr' => $opts[0]['cidr'],
                ],
                [
                    'name' => $opts[1]['name'],
                    'network_id' => $opts[1]['networkId'],
                    'tenant_id' => $opts[1]['tenantId'],
                    'ip_version' => $opts[1]['ipVersion'],
                    'cidr' => $opts[1]['cidr'],
                ],
            ],
        ];

        $this->setupMock('POST', 'v2.0/subnets', $expectedJson, [], 'subnets-post');

        $subnets = $this->subnet->bulkCreate($opts);

        $this->assertInternalType('array', $subnets);
        $this->assertCount(2, $subnets);
    }

    public function test_it_updates()
    {
        // Updatable attributes
        $this->subnet->name = 'bar';
        $this->subnet->gatewayIp = '192.168.199.1';

        $expectedJson = ['subnet' => [
            'name' => $this->subnet->name,
            'gateway_ip' => $this->subnet->gatewayIp,
        ]];

        $this->setupMock('PUT', 'v2.0/subnets/subnetId', $expectedJson, [], 'subnet-put');

        $this->assertInstanceOf(Subnet::class, $this->subnet->update());
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'v2.0/subnets/subnetId', null, [], 'subnet-get');

        $this->subnet->retrieve();

        $this->assertEquals('subnetId', $this->subnet->id);
        $this->assertEquals('192.0.0.0/8', $this->subnet->cidr);
        $this->assertEquals('192.0.0.1', $this->subnet->gatewayIp);
        $this->assertTrue($this->subnet->enableDhcp);
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'v2.0/subnets/subnetId', null, [], new Response(204));

        $this->subnet->delete();
    }
}
