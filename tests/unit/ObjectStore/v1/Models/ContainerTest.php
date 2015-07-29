<?php

namespace OpenStack\Test\ObjectStore\v1\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Models\Object;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class ContainerTest extends TestCase
{
    const NAME = 'test';

    private $container;

    public function setUp()
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

        $this->assertEquals(1, $this->container->objectCount);
        $this->assertEquals(['Book' => 'TomSawyer', 'Author' => 'SamuelClemens'], $this->container->metadata);
        $this->assertEquals(14, $this->container->bytesUsed);
    }

    public function test_Retrieve()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', self::NAME), 'HEAD_Container');
        $this->container->retrieve();
        $this->assertNotEmpty($this->container->metadata);
    }

    public function test_Get_Metadata()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', self::NAME), 'HEAD_Container');
        $this->assertEquals(['Book' => 'TomSawyer', 'Author' => 'SamuelClemens'], $this->container->getMetadata());
    }

    public function test_Merge_Metadata()
    {
        $headers = ['X-Container-Meta-Subject' => 'AmericanLiterature'];

        $this->setupMockResponse($this->setupMockRequest('POST', self::NAME, [], $headers), 'NoContent');

        $this->container->mergeMetadata(['Subject' => 'AmericanLiterature']);
    }

    public function test_Reset_Metadata()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', self::NAME), 'HEAD_Container');

        $headers = [
            'X-Container-Meta-Book'          => 'Middlesex',
            'X-Remove-Container-Meta-Author' => 'True',
        ];

        $this->setupMockResponse($this->setupMockRequest('POST', self::NAME, [], $headers), 'NoContent');

        $this->container->resetMetadata([
            'Book' => 'Middlesex',
        ]);
    }

    public function test_It_Creates()
    {
        $this->setupMockResponse($this->setupMockRequest('PUT', self::NAME), 'Created');
        $this->container->create(['name' => self::NAME]);
    }

    public function test_It_Deletes()
    {
        $this->setupMockResponse($this->setupMockRequest('DELETE', self::NAME), 'NoContent');
        $this->container->delete();
    }

    public function test_It_Gets_Object()
    {
        $object = $this->container->getObject('foo.txt');

        $this->assertInstanceOf(Object::class, $object);
        $this->assertEquals('foo.txt', $object->name);
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

        $request = $this->setupMockRequest('PUT', self::NAME . '/' . $objectName, $content, $headers);
        $this->setupMockResponse($request, 'Created');

        $this->container->createObject([
            'name'               => $objectName,
            'content'            => $content,
            'contentType'        => $headers['Content-Type'],
            'contentEncoding'    => $headers['Content-Encoding'],
            'contentDisposition' => $headers['Content-Disposition'],
            'deleteAfter'        => $headers['X-Delete-After'],
            'metadata'           => ['Author' => 'foo', 'genre' => 'bar'],
        ]);
    }

    public function test_it_lists_objects()
    {
        $req = $this->setupMockRequest('GET', 'test?limit=2&format=json');
        $this->setupMockResponse($req, 'GET_Container');

        foreach ($this->container->listObjects(['limit' => 2]) as $object) {
            $this->assertInstanceOf(Object::class, $object);
        }
    }

    public function test_true_is_returned_for_existing_object()
    {
        $req = $this->setupMockRequest('HEAD', 'test/bar');
        $this->setupMockResponse($req, new Response(200));

        $this->assertTrue($this->container->objectExists('bar'));
    }

    public function test_false_is_returned_for_non_existing_object()
    {
        $req = $this->setupMockRequest('HEAD', 'test/bar');
        $res = new Response(404);

        $e = new BadResponseError();
        $e->setRequest($req);
        $e->setResponse($res);

        $this->client
            ->send(Argument::is($req))
            ->willThrow($e);

        $this->assertFalse($this->container->objectExists('bar'));
    }
}