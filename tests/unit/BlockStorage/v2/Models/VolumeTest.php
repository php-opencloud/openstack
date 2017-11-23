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
        $this->volume->id = '1';
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

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'volumes/1', null, [], 'GET_volume');

        $this->volume->retrieve();

        $volumeImageMetadata = $this->volume->volumeImageMetadata;

        $this->assertInternalType('array', $volumeImageMetadata);
        $this->assertEquals($volumeImageMetadata['os_distro'], 'ubuntu');
        $this->assertEquals($volumeImageMetadata['os_version'], 'xenial');
        $this->assertEquals($volumeImageMetadata['hypervisor_type'], 'qemu');
        $this->assertEquals($volumeImageMetadata['os_variant'], 'ubuntu');
        $this->assertEquals($volumeImageMetadata['disk_format'], 'qcow2');
        $this->assertEquals($volumeImageMetadata['image_name'], 'Some Image Name x86_64');
        $this->assertEquals($volumeImageMetadata['image_id'], '54986297-8364-4baa-8435-812add437507');
        $this->assertEquals($volumeImageMetadata['architecture'], 'x86_64');
        $this->assertEquals($volumeImageMetadata['container_format'], 'bare');
        $this->assertEquals($volumeImageMetadata['min_disk'], '40');
        $this->assertEquals($volumeImageMetadata['os_type'], 'linux');
        $this->assertEquals($volumeImageMetadata['checksum'], 'bb3055b274fe72bc3406ffe9febe9fff');
        $this->assertEquals($volumeImageMetadata['min_ram'], '0');
        $this->assertEquals($volumeImageMetadata['size'], '6508557824');
    }

    public function test_it_merges_metadata()
    {
        $this->setupMock('GET', 'volumes/1/metadata', null, [], 'GET_metadata');

        $expectedJson = ['metadata' => [
            'foo' => 'newFoo',
            'bar' => '2',
            'baz' => 'bazVal',
        ]];

        $this->setupMock('PUT', 'volumes/1/metadata', $expectedJson, [], 'GET_metadata');

        $this->volume->mergeMetadata(['foo' => 'newFoo', 'baz' => 'bazVal']);
    }

    public function test_it_resets_metadata()
    {
        $expectedJson = ['metadata' => ['key1' => 'val1']];

        $this->setupMock('PUT', 'volumes/1/metadata', $expectedJson, [], 'GET_metadata');

        $this->volume->resetMetadata(['key1' => 'val1']);
    }
}
