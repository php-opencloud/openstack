<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Test\TestCase;

class FlavorTest extends TestCase
{
    private $flavor;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->flavor = new Flavor($this->client->reveal(), new Api());
        $this->flavor->id = 1;
    }

    public function test_it_retrieves_details()
    {
        $this->mockRequest('GET', 'flavors/1', 'flavor-get', null, []);

        $this->flavor->retrieve();

        self::assertEquals('m1.tiny', $this->flavor->name);
        self::assertEquals('1', $this->flavor->id);
        self::assertEquals(512, $this->flavor->ram);
        self::assertEquals(1, $this->flavor->vcpus);
        self::assertEquals(1, $this->flavor->disk);
    }

    public function test_it_creates()
    {
        $opts = [
            'name'  => 'test_flavor',
            'ram'   => 512,
            'vcpus' => 8,
            'disk'  => 80
        ];

        $expectedJson = ['flavor' => [
            'name'  => $opts['name'],
            'ram'   => $opts['ram'],
            'vcpus' => $opts['vcpus'],
            'disk'  => $opts['disk'],
        ]];

        $this->mockRequest('POST', 'flavors', 'flavor-post', $expectedJson, []);

        self::assertInstanceOf(Flavor::class, $this->flavor->create($opts));
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'flavors/1', new Response(204), null, []);

        $this->flavor->delete();
    }
}
