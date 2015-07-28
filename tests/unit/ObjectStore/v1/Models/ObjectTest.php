<?php

namespace OpenStack\Test\ObjectStore\v1\Models;

use GuzzleHttp\Stream\Stream;
use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Object;
use OpenStack\Test\TestCase;

class ObjectTest extends TestCase
{
    const CONTAINER = 'foo';
    const NAME = 'bar';

    private $object;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->object = new Object($this->client->reveal(), new Api());
        $this->object->containerName = self::CONTAINER;
        $this->object->name = self::NAME;
    }

    public function test_It_Creates()
    {
        $objectName = 'foo.txt';

        $headers = [
            'Content-Type'         => 'application/json',
            'Content-Disposition'  => 'attachment; filename=quot.pdf;',
            'Content-Encoding'     => 'gzip',
            'X-Delete-After'       => '500',
            'X-Object-Meta-Author' => 'foo',
            'X-Object-Meta-genre'  => 'bar',
        ];

        $content = json_encode(['foo' => 'bar']);

        $request = $this->setupMockRequest('PUT', self::CONTAINER . '/' . $objectName, $content, $headers);
        $this->setupMockResponse($request, 'Created');

        $this->object->create([
            'name'               => $objectName,
            'content'            => $content,
            'contentType'        => $headers['Content-Type'],
            'contentEncoding'    => $headers['Content-Encoding'],
            'contentDisposition' => $headers['Content-Disposition'],
            'deleteAfter'        => $headers['X-Delete-After'],
            'metadata'           => ['Author' => 'foo', 'genre' => 'bar'],
        ]);
    }

    public function test_Retrieve()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', self::CONTAINER . '/' . self::NAME), 'HEAD_Object');
        $this->object->retrieve();
        $this->assertNotEmpty($this->object->metadata);
    }

    public function test_Get_Metadata()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', self::CONTAINER . '/' . self::NAME), 'HEAD_Object');
        $this->assertEquals(['Book' => 'GoodbyeColumbus'], $this->object->getMetadata());
    }

    public function test_Merge_Metadata()
    {
        $headers = ['X-Object-Meta-Author' => 'foo'];
        $this->setupMockResponse($this->setupMockRequest('POST', self::CONTAINER . '/' . self::NAME, [], $headers), 'NoContent');
        $this->object->mergeMetadata(['Author' => 'foo']);
    }

    public function test_Reset_Metadata()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', self::CONTAINER . '/' . self::NAME), 'HEAD_Object');

        $headers = [
            'X-Object-Meta-Bar'         => 'Foo',
            'X-Remove-Object-Meta-Book' => 'True',
        ];

        $this->setupMockResponse($this->setupMockRequest('POST', self::CONTAINER . '/' . self::NAME, [], $headers), 'NoContent');

        $this->object->resetMetadata(['Bar' => 'Foo']);
    }

    public function test_It_Deletes()
    {
        $this->setupMockResponse($this->setupMockRequest('DELETE', self::CONTAINER . '/' . self::NAME), 'NoContent');
        $this->object->delete();
    }

    public function test_It_Downloads()
    {
        $this->setupMockResponse($this->setupMockRequest('GET', self::CONTAINER . '/' . self::NAME), 'GET_Object');

        $stream = $this->object->download();

        $this->assertInstanceOf(Stream::class, $stream);
        $this->assertEquals(14, $stream->getSize());
    }

    public function test_It_Copies()
    {
        $path = self::CONTAINER . '/' . self::NAME;
        $headers = ['Destination' => 'foo/bar'];

        $this->setupMockResponse($this->setupMockRequest('COPY', $path, null, $headers), 'Created');

        $this->object->copy([
            'destination' => $headers['Destination']
        ]);
    }
}