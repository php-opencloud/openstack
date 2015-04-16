<?php

namespace OpenStack\Test\Common\Resource;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Api\Operation;
use OpenStack\Common\Resource\Generator;
use OpenStack\Common\Resource\ResourceInterface;
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
        $this->resource = new TestResource(new Client(), new ComputeV2Api());
    }

    public function test_it_populates_from_response()
    {
        $response = new Response(200, [], Stream::factory(
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

    private function createOperationWith3AttachedResponses()
    {
        $operation = $this->prophesize(Operation::class);
        $operation->getValue('limit')->willReturn(null);
        $operation->hasParam('marker')->willReturn(true);

        $response1 = $this->getFixture('servers-page1');
        $response2 = $this->getFixture('servers-page2');
        $response3 = $this->getFixture('servers-empty');

        $operation->send()->willReturn($response1);

        $operation->setValue('marker', Argument::any())->shouldBeCalled();

        $operation->setValue('marker', '5')->will(function() use ($response2) {
            $this->send()->willReturn($response2);
        });

        $operation->setValue('marker', '10')->will(function() use ($response3) {
            $this->send()->willReturn($response3);
        });

        return $operation;
    }

    public function test_it_executes_operations_until_an_empty_response_is_received()
    {
        $operation = $this->createOperationWith3AttachedResponses();

        $count = 0;

        foreach ($this->resource->enumerate($operation->reveal()) as $item) {
            $count++;
            $this->assertInstanceOf(TestResource::class, $item);
        }

        $this->assertEquals(10, $count);
    }

    public function test_iteration_halts_when_total_has_been_reached()
    {
        $operation = $this->createOperationWith3AttachedResponses();
        $operation->getValue('limit')->willReturn(8);

        $count = 0;

        foreach ($this->resource->enumerate($operation->reveal()) as $item) {
            $count++;
        }

        $this->assertEquals(8, $count);
    }

    public function test_map_fn_is_invoked_in_generators()
    {
        $operation = $this->createOperationWith3AttachedResponses();

        $count = 0;

        $fn = function (ResourceInterface $resource) use (&$count) {
            $count++;
        };

        foreach ($this->resource->enumerate($operation->reveal(), $fn) as $item) {}

        $this->assertEquals(10, $count);
    }
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