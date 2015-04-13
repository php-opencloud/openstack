<?php

namespace OpenStack\Test\Common\Resource;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use OpenStack\Common\Resource\AbstractResource;
use Prophecy\PhpUnit\ProphecyTestCase;

class AbstractResourceTest extends ProphecyTestCase
{
    private $resource;

    public function setUp()
    {
        $this->resource = new TestResource(new Client());
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
}

class TestResource extends AbstractResource
{
    /** @var string */
    public $bar;

    protected $jsonKey = 'foo';

    public function getAttrs(array $keys)
    {
        return parent::getAttrs($keys);
    }
}