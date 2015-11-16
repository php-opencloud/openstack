<?php

namespace OpenStack\Test\Networking\v2;

use OpenStack\Networking\v2\Api;
use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Models\Subnet;
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

        $this->setupMock('POST', 'v2.0/networks', $expectedJson, [], 'network-post');

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

        $this->setupMock('POST', 'v2.0/networks', $expectedJson, [], 'networks-post');

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

    public function test_it_creates_an_subnet()
    {
        $opts = [
            'name' => 'foo',
            'networkId' => 'networkId',
            'tenantId' => 'tenantId',
            'ipVersion' => 4,
            'cidr' => '192.168.199.0/24',
        ];

        $expectedJson = ['subnet' => [
            'name' => $opts['name'],
            'network_id' => $opts['networkId'],
            'tenant_id' => $opts['tenantId'],
            'ip_version' => $opts['ipVersion'],
            'cidr' => $opts['cidr'],
        ]];

        $this->setupMock('POST', 'v2.0/subnets', $expectedJson, [], 'subnet-post');

        $this->assertInstanceOf(Subnet::class, $this->service->createSubnet($opts));
    }

    public function test_it_bulk_creates_subnets()
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

        $subnets = $this->service->createSubnets($opts);

        $this->assertInternalType('array', $subnets);
        $this->assertCount(2, $subnets);
    }

    public function test_it_gets_an_subnet()
    {
      $subnet = $this->service->getSubnet('subnetId');

      $this->assertInstanceOf(Subnet::class, $subnet);
      $this->assertEquals('subnetId', $subnet->id);
    }
}
