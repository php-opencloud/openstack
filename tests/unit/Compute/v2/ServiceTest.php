<?php

namespace OpenStack\Test\Compute\v2;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\HypervisorStatistic;
use OpenStack\Compute\v2\Models\Host;
use OpenStack\Compute\v2\Models\AvailabilityZone;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Compute\v2\Models\Keypair;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Compute\v2\Models\Hypervisor;
use OpenStack\Compute\v2\Service;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class ServiceTest extends TestCase
{
    /** @var Service */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_creates_servers()
    {
        $opts = [
            'name' => 'foo',
            'imageId' => '',
            'flavorId' => '',
        ];

        $expectedJson = ['server' => [
            'name' => $opts['name'],
            'imageRef' => $opts['imageId'],
            'flavorRef' => $opts['flavorId'],
        ]];

        $this->mockRequest('POST', 'servers', 'server-post', $expectedJson, []);

        self::assertInstanceOf(Server::class, $this->service->createServer($opts));
    }

    public function test_it_lists_servers()
    {
        $this->mockRequest('GET', ['path' => 'servers', 'query' => ['limit' => 5]], 'servers-get');

        foreach ($this->service->listServers(false, ['limit' => 5]) as $server) {
            self::assertInstanceOf(Server::class, $server);
        }
    }

    public function test_it_gets_a_server()
    {
        $server = $this->service->getServer([
            'id' => 'serverId'
        ]);

        self::assertInstanceOf(Server::class, $server);
        self::assertEquals('serverId', $server->id);
    }

    public function test_it_lists_flavors()
    {
        $this->mockRequest('GET', ['path' => 'flavors', 'query' => ['limit' => 5]], 'flavors-get');

        $count = 0;

        foreach ($this->service->listFlavors(['limit' => 5]) as $flavor) {
            ++$count;
            self::assertInstanceOf(Flavor::class, $flavor);
        }

        self::assertEquals(5, $count);
    }

    public function test_it_gets_a_flavor()
    {
        $flavor = $this->service->getFlavor([
            'id' => 'flavorId'
        ]);

        self::assertInstanceOf(Flavor::class, $flavor);
        self::assertEquals('flavorId', $flavor->id);
    }

    public function test_it_lists_images()
    {
        $this->mockRequest('GET', ['path' => 'images', 'query' => ['limit' => 5]], 'images-get');

        foreach ($this->service->listImages(['limit' => 5]) as $image) {
            self::assertInstanceOf(Image::class, $image);
        }
    }

    public function test_it_gets_an_image()
    {
        $image = $this->service->getImage([
            'id' => 'imageId'
        ]);

        self::assertInstanceOf(Image::class, $image);
        self::assertEquals('imageId', $image->id);
    }

    public function test_it_lists_keypairs()
    {
        $this->mockRequest('GET', 'os-keypairs', 'keypairs-get');

        foreach ($this->service->listKeypairs() as $keypair) {
            self::assertInstanceOf(Keypair::class, $keypair);
        }
    }

    public function test_it_gets_hypervisor_statistics()
    {
        $this->mockRequest('GET', 'os-hypervisors/statistics', 'hypervisor-statistic-get');

        $hypervisorStats = $this->service->getHypervisorStatistics();

        self::assertInstanceOf(HypervisorStatistic::class, $hypervisorStats);
    }

    public function test_it_lists_hypervisors()
    {
        $this->mockRequest('GET', 'os-hypervisors', 'hypervisors-get');

        foreach ($this->service->listHypervisors(false) as $hypervisor) {
            self::assertInstanceOf(Hypervisor::class, $hypervisor);
        }
    }

    public function test_it_gets_hypervisor()
    {
        $this->mockRequest('GET', 'os-hypervisors/1234', 'hypervisor-get');

        $hypervisor = $this->service->getHypervisor(['id' => 1234]);
        $hypervisor->retrieve();

        self::assertInstanceOf(Hypervisor::class, $hypervisor);
    }

    public function test_it_lists_hosts()
    {
        $this->mockRequest('GET', ['path' => 'os-hosts', 'query' => ['limit' => 5]], 'hosts-get');

        foreach ($this->service->listHosts(['limit' => 5]) as $host) {
            self::assertInstanceOf(Host::class, $host);
        }
    }

    public function test_it_gets_host()
    {
        $this->mockRequest('GET', 'os-hosts/b6e4adbc193d428ea923899d07fb001e', 'host-get');

        $host = $this->service->getHost(['name' => 'b6e4adbc193d428ea923899d07fb001e']);
        $host->retrieve();

        self::assertInstanceOf(Host::class, $host);
    }

    public function test_it_lists_availability_zones()
    {
        $this->mockRequest(
            'GET',
            ['path' => 'os-availability-zone/detail', 'query' => ['limit' => 5]],
            'availability-zones-get'
        );

        foreach ($this->service->listAvailabilityZones(['limit' => 5]) as $zone) {
            self::assertInstanceOf(AvailabilityZone::class, $zone);
        }
    }
}
