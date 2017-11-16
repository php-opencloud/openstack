<?php

namespace OpenStack\Test\Common\Resource;

use function GuzzleHttp\Psr7\stream_for;
use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\ResourceInterface;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class AbstractResourceTest extends TestCase
{
    /** @var TestResource */
    private $resource;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->resource = new TestResource();
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

    public function test_it_populates_model_objects_from_arrays()
    {
        $tr = new TestResource();
        $this->resource->populateFromArray(['child' => $tr]);

        $this->assertEquals($this->resource->child, $tr);
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

    public function test_it_returns_a_model_instance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->resource->model(TestResource::class));
    }

    public function test_it_populates_models_from_response()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->resource->model(TestResource::class, new Response(200)));
    }

    public function test_it_populates_models_from_arrays()
    {
        $data = ['flavor' => [], 'image' => []];
        $this->assertInstanceOf(ResourceInterface::class, $this->resource->model(TestResource::class, $data));
    }
}

class TestResource extends AbstractResource
{
    protected $resourceKey = 'foo';

    /** @var string */
    public $bar;

    public $id;

    /** @var \DateTimeImmutable */
    public $created;

    /** @var []TestResource */
    public $children;

    /** @var TestResource */
    public $child;

    protected function getAliases(): array
    {
        $aliases = parent::getAliases();
        $aliases['created'] = new Alias('created', \DateTimeImmutable::class);
        $aliases['child'] = new Alias('child', TestResource::class);
        $aliases['children'] = new Alias('children', TestResource::class, true);
        return $aliases;
    }

    public function getAttrs(array $keys)
    {
        return parent::getAttrs($keys);
    }
}
