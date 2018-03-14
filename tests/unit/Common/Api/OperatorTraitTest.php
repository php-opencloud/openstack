<?php

namespace OpenStack\Test\Common\Api;

use function GuzzleHttp\Psr7\uri_for;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\OperatorTrait;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\ResourceInterface;
use OpenStack\Test\Fixtures\ComputeV2Api;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class OperatorTraitTest extends TestCase
{
    /** @var TestOperator */
    private $operator;

    private $def;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->def = [
            'method' => 'GET',
            'path'   => 'test',
            'params' => [],
        ];

        $this->operator = new TestOperator($this->client->reveal(), new ComputeV2Api());
    }

    public function test_it_returns_operations()
    {
        $this->assertInstanceOf(
            Operation::class,
            $this->operator->getOperation($this->def, [])
        );
    }

    public function test_it_sends_a_request_when_operations_are_executed()
    {
        $this->client->request('GET', 'test', ['headers' => []])->willReturn(new Response());

        $this->operator->execute($this->def, []);

        $this->addToAssertionCount(1);
    }

    public function test_it_sends_a_request_when_async_operations_are_executed()
    {
        $this->client->requestAsync('GET', 'test', ['headers' => []])->willReturn(new Promise());

        $this->operator->executeAsync($this->def, []);

        $this->addToAssertionCount(1);
    }

    public function test_it_wraps_sequential_ops_in_promise_when_async_is_appended_to_method_name()
    {
        $promise = $this->operator->createAsync('something');

        $this->assertInstanceOf(Promise::class, $promise);

        $promise->then(function ($val) {
            $this->assertEquals('Created something', $val);
        });

        $promise->wait();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_it_throws_exception_when_async_is_called_on_a_non_existent_method()
    {
        $this->operator->fooAsync();
    }

    /**
     * @expectedException \Exception
     */
    public function test_undefined_methods_result_in_error()
    {
        $this->operator->foo();
    }

    public function test_it_returns_a_model_instance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model(TestResource::class));
    }

    public function test_it_populates_models_from_response()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model(TestResource::class, new Response(200)));
    }

    public function test_it_populates_models_from_arrays()
    {
        $data = ['flavor' => [], 'image' => []];
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model(TestResource::class, $data));
    }
}

class TestResource extends AbstractResource
{
}

class TestOperator
{
    use OperatorTrait;

    public function create($str)
    {
        return 'Created ' . $str;
    }

    public function fail()
    {
    }
}
