<?php

namespace OpenStack\Test\BlockStorage\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\BlockStorage\v2\Api;
use OpenStack\BlockStorage\v2\Models\VolumeType;
use OpenStack\Test\TestCase;

class VolumeTypeTest extends TestCase
{
    /** @var VolumeType */
    protected $volumeType;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->volumeType = new VolumeType($this->client->reveal(), new Api());
        $this->volumeType->id = '1';
    }

    public function test_it_updates()
    {
        $expectedJson = ['volume_type' => ['name' => 'foo']];

        $this->mockRequest('PUT', 'types/1', 'GET_type', $expectedJson, []);

        $this->volumeType->name = 'foo';
        $this->volumeType->update();
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'types/1', new Response(204), null, []);

        $this->volumeType->delete();
    }
}
