<?php

namespace OpenStack\Test\Common\Resource;

use function GuzzleHttp\Psr7\stream_for;

use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Generator;
use OpenStack\Test\Fixtures\ComputeV2Api;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class AbstractResourceTest extends TestCase
{
    private $resource;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;
        $this->resource = new TestResource($this->client->reveal(), new ComputeV2Api());
    }

    public function test_it_populates_from_response()
    {
        $response = new Response(200, ['Content-Type' => 'application/json'], stream_for(
            json_encode(['foo' => ['bar' => '1']])
        ));

        $this->resource->populateFromResponse($response);

        $this->assertEquals('1', $this->resource->bar);
    }

    public function test_it_gets_attrs()
    {
        $this->resource->bar = 'foo';

        $this->assertEquals(['bar' => 'foo'], $this->resource->getAttrs(['bar']));
    }

    public function test_it_executes_operations_until_a_204_is_received()
    {
        $this->client
            ->request('GET', 'servers', ['headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('servers-page1'));

        $this->client
            ->request('GET', 'servers', ['query' => ['marker' => '5'], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn(new Response(204));

        $count = 0;

        $api = new ComputeV2Api();

        foreach ($this->resource->enumerate($api->getServers()) as $item) {
            $count++;
            $this->assertInstanceOf(TestResource::class, $item);
        }

        $this->assertEquals(5, $count);
    }

//    public function test_it_executes_operations_until_an_empty_response()
//    {
//        $this->client
//            ->request('GET', 'servers', ['headers' => []])
//            ->shouldBeCalled()
//            ->willReturn($this->getFixture('servers-page1'));
//
//        $this->client
//            ->request('GET', 'servers', ['query' => ['marker' => '5'], 'headers' => []])
//            ->shouldBeCalled()
//            ->willReturn($this->getFixture('servers-empty'));
//
//        $count = 0;
//
//        $api = new ComputeV2Api();
//
//        foreach ($this->resource->enumerate($api->getServers()) as $item) {
//            $count++;
//            $this->assertInstanceOf(TestResource::class, $item);
//        }
//
//        $this->assertEquals(5, $count);
//    }

//    public function test_iteration_halts_when_total_has_been_reached()
//    {
//        $operation = $this->createOperationWith3AttachedResponses();
//        $operation->getValue('limit')->willReturn(8);
//
//        $count = 0;
//
//        foreach ($this->resource->enumerate($operation->reveal()) as $item) {
//            $count++;
//        }
//
//        $this->assertEquals(8, $count);
//    }
//
//    public function test_map_fn_is_invoked_in_generators()
//    {
//        $operation = $this->createOperationWith3AttachedResponses();
//
//        $count = 0;
//
//        $fn = function (ResourceInterface $resource) use (&$count) {
//            $count++;
//        };
//
//        foreach ($this->resource->enumerate($operation->reveal(), $fn) as $item) {
//        }
//
//        $this->assertEquals(10, $count);
//    }
}

class TestResource extends AbstractResource
{
    protected $resourceKey = 'foo';
    protected $resourcesKey = 'servers';
    protected $markerKey = 'id';

    public $bar;
    public $id;

    public function getAttrs(array $keys)
    {
        return parent::getAttrs($keys);
    }
}