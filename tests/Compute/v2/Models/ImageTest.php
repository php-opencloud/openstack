<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Test\TestCase;

class ImageTest extends TestCase
{
    private $image;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->image = new Image($this->client->reveal(), new Api());
        $this->image->id = 'imageId';
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'images/imageId');
        $this->setupMockResponse($request, 'image-get');

        $this->image->retrieve();

        $metadata = [
            "architecture" => "x86_64",
            "auto_disk_config" => "True",
            "kernel_id" => "nokernel",
            "ramdisk_id" => "nokernel"
        ];

        $this->assertEquals(new \DateTimeImmutable('2011-01-01T01:02:03Z'), $this->image->created);
        $this->assertEquals($metadata, $this->image->metadata);
        $this->assertEquals(0, $this->image->minDisk);
        $this->assertEquals(0, $this->image->minRam);
        $this->assertEquals('fakeimage7', $this->image->name);
        $this->assertEquals(100, $this->image->progress);
        $this->assertEquals('ACTIVE', $this->image->status);
        $this->assertEquals(new \DateTimeImmutable('2011-01-01T01:02:03Z'), $this->image->updated);
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'images/imageId');
        $this->setupMockResponse($request, new Response(204));

        $this->image->delete();
    }

    public function test_it_retrieves_metadata()
    {
        $request = $this->setupMockRequest('GET', 'images/imageId/metadata');
        $this->setupMockResponse($request, 'server-metadata-get');

        $metadata = $this->image->getMetadata();

        $this->assertEquals('x86_64', $metadata['architecture']);
        $this->assertEquals('True', $metadata['auto_disk_config']);
        $this->assertEquals('nokernel', $metadata['kernel_id']);
        $this->assertEquals('nokernel', $metadata['ramdisk_id']);
    }

    public function test_it_sets_metadata()
    {
        $metadata = ['foo' => '1', 'bar' => '2'];

        $expectedJson = ['metadata' => $metadata];

        $request = $this->setupMockRequest('PUT', 'images/imageId/metadata', $expectedJson);
        $this->setupMockResponse($request, $this->createResponse(200, [], $expectedJson));

        $metadata = $this->image->resetMetadata($metadata);

        $this->assertEquals('1', $metadata['foo']);
    }

    public function test_it_updates_metadata()
    {
        $metadata = ['foo' => '1'];

        $expectedJson = ['metadata' => $metadata];

        $request = $this->setupMockRequest('POST', 'images/imageId/metadata', $expectedJson);
        $this->setupMockResponse($request, $this->createResponse(200, [], array_merge_recursive($expectedJson, ['metadata' => ['bar' => '2']])));

        $metadata = $this->image->mergeMetadata($metadata);

        $this->assertEquals('1', $metadata['foo']);
        $this->assertEquals('2', $metadata['bar']);
    }

    public function test_it_retrieves_a_metadata_item()
    {
        $request = $this->setupMockRequest('GET', 'images/imageId/metadata/fooKey');
        $this->setupMockResponse($request, $this->createResponse(200, [], ['metadata' => ['fooKey' => 'bar']]));

        $value = $this->image->getMetadataItem('fooKey');

        $this->assertEquals('bar', $value);
    }

    public function test_it_deletes_a_metadata_item()
    {
        $request = $this->setupMockRequest('DELETE', 'images/imageId/metadata/fooKey');
        $this->setupMockResponse($request, new Response(204));

        $this->assertNull($this->image->deleteMetadataItem('fooKey'));
    }
}
