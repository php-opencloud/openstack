<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Test\TestCase;

class ImageTest extends TestCase
{
    private $image;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->image = new Image($this->client->reveal(), new Api());
        $this->image->id = 'imageId';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'images/imageId', 'image-get');

        $this->image->retrieve();

        $metadata = [
            "architecture" => "x86_64",
            "auto_disk_config" => "True",
            "kernel_id" => "nokernel",
            "ramdisk_id" => "nokernel"
        ];

        self::assertEquals(new \DateTimeImmutable('2011-01-01T01:02:03Z'), $this->image->created);
        self::assertEquals($metadata, $this->image->metadata);
        self::assertEquals(0, $this->image->minDisk);
        self::assertEquals(0, $this->image->minRam);
        self::assertEquals('fakeimage7', $this->image->name);
        self::assertEquals(100, $this->image->progress);
        self::assertEquals('ACTIVE', $this->image->status);
        self::assertEquals(new \DateTimeImmutable('2011-01-01T01:02:03Z'), $this->image->updated);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'images/imageId', new Response(204));

        $this->image->delete();
    }

    public function test_it_retrieves_metadata()
    {
        $this->mockRequest('GET', 'images/imageId/metadata', 'server-metadata-get');

        $metadata = $this->image->getMetadata();

        self::assertEquals('x86_64', $metadata['architecture']);
        self::assertEquals('True', $metadata['auto_disk_config']);
        self::assertEquals('nokernel', $metadata['kernel_id']);
        self::assertEquals('nokernel', $metadata['ramdisk_id']);
    }

    public function test_it_sets_metadata()
    {
        $metadata = ['foo' => '1', 'bar' => '2'];

        $expectedJson = ['metadata' => $metadata];

        $response = $this->createResponse(200, [], $expectedJson);
        $this->mockRequest('PUT', 'images/imageId/metadata', $response, $expectedJson);

        $this->image->resetMetadata($metadata);

        self::assertEquals('1', $this->image->metadata['foo']);
    }

    public function test_it_updates_metadata()
    {
        $metadata = ['foo' => '1'];

        $expectedJson = ['metadata' => $metadata];

        $response = $this->createResponse(200, [], array_merge_recursive($expectedJson, ['metadata' => ['bar' => '2']]));
        $this->mockRequest('POST', 'images/imageId/metadata', $response, $expectedJson);

        $this->image->mergeMetadata($metadata);

        self::assertEquals('1', $this->image->metadata['foo']);
        self::assertEquals('2', $this->image->metadata['bar']);
    }

    public function test_it_retrieves_a_metadata_item()
    {
        $response = $this->createResponse(200, [], ['metadata' => ['fooKey' => 'bar']]);
        $this->mockRequest('GET', 'images/imageId/metadata/fooKey', $response);

        $value = $this->image->getMetadataItem('fooKey');

        self::assertEquals('bar', $value);
    }

    public function test_it_deletes_a_metadata_item()
    {
        $this->mockRequest('DELETE', 'images/imageId/metadata/fooKey', new Response(204));

        $this->image->deleteMetadataItem('fooKey');
    }
}
