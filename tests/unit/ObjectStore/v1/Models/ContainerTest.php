<?php

namespace OpenStack\Test\ObjectStore\v1\Models;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\StorageObject;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class ContainerTest extends TestCase
{
    const NAME = 'test';

    private $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->container = new Container($this->client->reveal(), new Api());
        $this->container->name = self::NAME;
    }

    public function test_Populate_From_Response()
    {
        $response = $this->getFixture('HEAD_Container');

        $this->container->populateFromResponse($response);

        self::assertEquals(1, $this->container->objectCount);
        self::assertEquals(
            [
                'Book'      => 'TomSawyer',
                'Author'    => 'SamuelClemens',
                'UPPERCASE' => 'UPPERCASE',
                'lowercase' => 'lowercase',
            ],
            $this->container->metadata
        );
        self::assertEquals(14, $this->container->bytesUsed);
    }

    public function test_Retrieve()
    {
        $this->mockRequest('HEAD', self::NAME, 'HEAD_Container', null, []);

        $this->container->retrieve();
        self::assertNotEmpty($this->container->metadata);
    }

    public function test_Get_Metadata()
    {
        $this->mockRequest('HEAD', self::NAME, 'HEAD_Container', null, []);

        self::assertEquals(
            [
                'Book'      => 'TomSawyer',
                'Author'    => 'SamuelClemens',
                'UPPERCASE' => 'UPPERCASE',
                'lowercase' => 'lowercase',

            ],
            $this->container->getMetadata()
        );
    }

    public function test_Merge_Metadata()
    {
        $headers = ['X-Container-Meta-Subject' => 'AmericanLiterature'];

        $this->mockRequest('POST', self::NAME, 'NoContent', [], $headers);

        $this->container->mergeMetadata(['Subject' => 'AmericanLiterature']);
    }

    public function test_Reset_Metadata()
    {
        $this->mockRequest('HEAD', self::NAME, 'HEAD_Container', null, []);

        $headers = [
            'X-Container-Meta-Book'          => 'Middlesex',
            'X-Remove-Container-Meta-Author' => 'True',
            'X-Remove-Container-Meta-UPPERCASE' => 'True',
            'X-Remove-Container-Meta-lowercase' => 'True',
        ];

        $this->mockRequest('POST', self::NAME, 'NoContent', [], $headers);

        $this->container->resetMetadata([
            'Book' => 'Middlesex',
        ]);
    }

    public function test_It_Creates()
    {
        $this->mockRequest('PUT', self::NAME, 'Created', null, []);
        $this->container->create(['name' => self::NAME]);
    }

    public function test_It_Deletes()
    {
        $this->mockRequest('DELETE', self::NAME, 'NoContent', null, []);
        $this->container->delete();
    }

    public function test_It_Gets_Object()
    {
        $object = $this->container->getObject('foo.txt');

        self::assertInstanceOf(StorageObject::class, $object);
        self::assertEquals('foo.txt', $object->name);
    }

    public function test_It_Create_Objects()
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

        $this->mockRequest('PUT', self::NAME . '/' . $objectName, 'Created', $content, $headers);

        /** @var StorageObject $storageObject */
        $storageObject = $this->container->createObject([
            'name'               => $objectName,
            'content'            => $content,
            'contentType'        => $headers['Content-Type'],
            'contentEncoding'    => $headers['Content-Encoding'],
            'contentDisposition' => $headers['Content-Disposition'],
            'deleteAfter'        => $headers['X-Delete-After'],
            'metadata'           => ['Author' => 'foo', 'genre' => 'bar'],
        ]);

        self::assertEquals('foo.txt', $storageObject->name);
        self::assertEquals(self::NAME, $storageObject->containerName);
    }

    public function test_it_lists_objects()
    {
        $this->mockRequest('GET', ['path' => 'test', 'query' => ['limit' => 2, 'format' => 'json']], 'GET_Container');

        $objects = iterator_to_array($this->container->listObjects(['limit' => 2]));

        self::assertEquals(2, count($objects));

        $expected = [
            [
                'name'          => 'goodbye',
                'contentLength' => '14',
                'lastModified'  => new \DateTimeImmutable('2014-01-15T16:41:49.390270'),
                'contentType'   => 'application/octet-stream',
                'hash'          => '451e372e48e0f6b1114fa0724aa79fa1',
            ],
            [
                'name'          => 'helloworld.json',
                'contentLength' => '12',
                'lastModified'  => new \DateTimeImmutable('2014-01-15T16:37:43.427570'),
                'contentType'   => 'application/json',
                'hash'          => 'ed076287532e86365e841e92bfc50d8c',
            ],
        ];

        for ($i = 0; $i < count($objects); $i++) {
            $exp = $expected[$i];
            /** @var StorageObject $obj */
            $obj = $objects[$i];

            foreach ($exp as $attr => $attrVal) {
                self::assertEquals($attrVal, $obj->{$attr});
            }
        }
    }

    public function test_true_is_returned_for_existing_object()
    {
        $this->mockRequest('HEAD', 'test/bar', new Response(200), null, []);

        self::assertTrue($this->container->objectExists('bar'));
    }

    public function test_false_is_returned_for_non_existing_object()
    {
        $e = new BadResponseError();
        $e->setRequest(new Request('HEAD', 'test/bar'));
        $e->setResponse(new Response(404));

        $this->mockRequest('HEAD', 'test/bar', $e);

        self::assertFalse($this->container->objectExists('bar'));
    }

    public function test_other_exceptions_are_thrown()
    {
        $e = new BadResponseError();
        $e->setRequest(new Request('HEAD', 'test/bar'));
        $e->setResponse(new Response(500));

        $this->mockRequest('HEAD', 'test/bar', $e);
        $this->expectException(BadResponseError::class);

        $this->container->objectExists('bar');
    }

    public function test_valid_segment_index_format()
    {
        self::assertTrue($this->container->isValidSegmentIndexFormat("%03d"));
        self::assertTrue($this->container->isValidSegmentIndexFormat("%05d"));
        self::assertFalse($this->container->isValidSegmentIndexFormat("%d"));
        self::assertFalse($this->container->isValidSegmentIndexFormat("d"));
    }

    public function test_it_chunks_according_to_provided_segment_size()
    {
        $stream = function_exists('\GuzzleHttp\Psr7\stream_for')
            ? \GuzzleHttp\Psr7\stream_for(implode('', range('A', 'X')))
            : \GuzzleHttp\Psr7\Utils::streamFor(implode('', range('A', 'X')));

        $data = [
            'name'               => 'object',
            'stream'             => $stream,
            'segmentSize'        => 10,
            'segmentPrefix'      => 'objectPrefix',
            'segmentContainer'   => 'segments',
            'segmentIndexFormat' => '%03d',
        ];

        // check container creation
        $e = new BadResponseError();
        $e->setRequest(new Request('HEAD', 'segments'));
        $e->setResponse(new Response(404));

        $this->mockRequest('HEAD', 'segments', $e);

        $this->mockRequest('PUT', 'segments', new Response(201), null, []);

        // The stream has size 24 so we expect three segments.
        $this->mockRequest('PUT', 'segments/objectPrefix/001', new Response(201), $stream->read(10), []);
        $this->mockRequest('PUT', 'segments/objectPrefix/002', new Response(201), $stream->read(10), []);
        $this->mockRequest('PUT', 'segments/objectPrefix/003', new Response(201), $stream->read(10), []);
        $this->mockRequest('PUT', 'test/object', new Response(201), null, ['X-Object-Manifest' => 'segments/objectPrefix']);

        $stream->rewind();

        $this->container->createLargeObject($data);
    }
}
