<?php

namespace OpenStack\Test\Compute\v2\Models;

use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Test\TestCase;

class FlavorTest extends TestCase
{
    private $flavor;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->flavor = new Flavor($this->client->reveal(), new Api());
        $this->flavor->id = 1;
    }

    public function test_it_retrieves_details()
    {
        $request = $this->setupMockRequest('GET', 'flavors/1');
        $this->setupMockResponse($request, 'flavor-get');

        $this->flavor->retrieve();

        $this->assertEquals('m1.tiny', $this->flavor->name);
        $this->assertEquals('1', $this->flavor->id);
        $this->assertEquals(512, $this->flavor->ram);
        $this->assertEquals(1, $this->flavor->vcpus);
        $this->assertEquals(1, $this->flavor->disk);
    }
}