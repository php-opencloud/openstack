<?php

namespace OpenStack\Test\BlockStorage\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\BlockStorage\v2\Api;
use OpenStack\BlockStorage\v2\Models\Volume;
use OpenStack\Test\TestCase;

class VolumeTest extends TestCase
{
    /** @var Volume */
    private $volume;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->volume = new Volume($this->client->reveal(), new Api());
        $this->volume->id = 1;
    }

    public function test_it_updates()
    {
        $this->volume->name = 'foo';
        $this->volume->description = 'bar';

        $expectedJson = ['volume' => ['name' => 'foo', 'description' => 'bar']];
        $this->setupMock('PUT', 'volumes/1', $expectedJson, [], 'GET_volume');

        $this->volume->update();
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'volumes/1', null, [], new Response(204));

        $this->volume->delete();
    }
}