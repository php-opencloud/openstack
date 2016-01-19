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

    public function test_it_populates_datetimes_from_arrays()
    {
        $dt = new \DateTimeImmutable('2015');

        $this->resource->populateFromArray(['created' => '2015']);

        $this->assertEquals($this->resource->created, $dt);
    }

    public function test_it_populates_arrays_from_arrays()
    {
        $this->resource->populateFromArray(['children' => [$this->resource, $this->resource]]);

        $this->assertInstanceOf(TestResource::class, $this->resource->children[0]);
    }

    public function test_it_gets_attrs()
    {
        $this->resource->bar = 'foo';

        $this->assertEquals(['bar' => 'foo'], $this->resource->getAttrs(['bar']));
    }

    public function test_it_executes_with_state()
    {
        $this->resource->id = 'foo';
        $this->resource->bar = 'bar';

        $expectedJson = ['id' => 'foo', 'bar' => 'bar'];

        $this->setupMock('GET', 'foo', $expectedJson, [], new Response(204));

        $this->resource->executeWithState((new ComputeV2Api())->test());
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

    public function test_it_invokes_function_if_provided()
    {
        $this->client
            ->request('GET', 'servers', ['headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('servers-page1'));

        $this->client
            ->request('GET', 'servers', ['query' => ['marker' => '5'], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn(new Response(204));

        $api = new ComputeV2Api();

        $count = 0;

        $fn = function () use (&$count) {
            $count++;
        };

        foreach ($this->resource->enumerate($api->getServers(), [], $fn) as $item) {
        }

        $this->assertEquals(5, $count);
    }

    public function test_it_halts_when_user_provided_limit_is_reached()
    {
        $this->client
            ->request('GET', 'servers', ['query' => ['limit' => 2], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('servers-page1'));

        $count = 0;

        $api = new ComputeV2Api();

        foreach ($this->resource->enumerate($api->getServers(), ['limit' => 2]) as $item) {
            $count++;
        }

        $this->assertEquals(2, $count);
    }
}

class TestResource extends AbstractResource
{
    protected $resourceKey = 'foo';
    protected $resourcesKey = 'servers';
    protected $markerKey = 'id';

    /** @var string */
    public $bar;

    public $id;

    /** @var \DateTimeImmutable */
    public $created;

    /** @var []TestResource */
    public $children;

    public function getAttrs(array $keys)
    {
        return parent::getAttrs($keys);
    }
}
