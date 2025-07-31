<?php

namespace OpenStack\Test\BlockStorage\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\BlockStorage\v2\Api;
use OpenStack\BlockStorage\v2\Models\Snapshot;
use OpenStack\Test\TestCase;

class SnapshotTest extends TestCase
{
    /** @var Snapshot */
    protected $snapshot;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->snapshot = new Snapshot($this->client->reveal(), new Api());
        $this->snapshot->id = '1';
    }

    public function test_it_updates()
    {
        $this->snapshot->name = 'foo';
        $this->snapshot->description = 'bar';

        $expectedJson = ['snapshot' => ['name' => 'foo', 'description' => 'bar']];
        $this->mockRequest('PUT', 'snapshots/1', 'GET_snapshot', $expectedJson, []);

        $this->snapshot->update();
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'snapshots/1', new Response(204), null, []);

        $this->snapshot->delete();
    }

    public function test_it_gets_metadata()
    {
        $this->mockRequest('GET', 'snapshots/1/metadata', 'GET_metadata', null, []);

        $expected = [
            'foo' => '1',
            'bar' => '2',
        ];

        self::assertEquals($expected, $this->snapshot->getMetadata());
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'snapshots/1', 'GET_snapshot', null, []);

        $this->snapshot->retrieve();
    }

    public function test_it_merges_metadata()
    {
        $this->mockRequest('GET', 'snapshots/1/metadata', 'GET_metadata', null, []);

        $expectedJson = ['metadata' => [
            'foo' => 'newFoo',
            'bar' => '2',
            'baz' => 'bazVal',
        ]];

        $this->mockRequest('PUT', 'snapshots/1/metadata', 'GET_metadata', $expectedJson, []);

        $this->snapshot->mergeMetadata(['foo' => 'newFoo', 'baz' => 'bazVal']);
    }

    public function test_it_resets_metadata()
    {
        $expectedJson = ['metadata' => ['key1' => 'val1']];

        $this->mockRequest('PUT', 'snapshots/1/metadata', 'GET_metadata', $expectedJson, []);

        $this->snapshot->resetMetadata(['key1' => 'val1']);
    }
}
