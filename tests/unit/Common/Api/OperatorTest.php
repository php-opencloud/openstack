<?php

namespace OpenStack\Test\Common\Api;

use function GuzzleHttp\Psr7\uri_for;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use OpenStack\Common\Api\Operator;
use OpenStack\Common\Resource\ResourceInterface;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Test\Fixtures\ComputeV2Api;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class OperatorTest extends ProphecyTestCase
{
    private $operator;
    private $client;
    private $def;

    public function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);

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
            'OpenStack\Common\Api\Operation',
            $this->operator->getOperation($this->def, [])
        );
    }

    public function test_it_sends_a_request_when_operations_are_executed()
    {
        $this->client->request('GET', 'test', ['headers' => []])->willReturn(new Request('GET', 'test'));

        $this->operator->execute($this->def, []);
    }

    public function test_it_sends_a_request_when_async_operations_are_executed()
    {
        $this->client->requestAsync('GET', 'test', ['headers' => []])->willReturn(new Promise());

        $this->operator->executeAsync($this->def, []);
    }

    public function test_it_returns_a_model_instance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model(Server::class));
    }

    public function test_it_populates_models_from_response()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model(Server::class, new Response(200)));
    }

    public function test_it_populates_models_from_arrays()
    {
        $data = ['flavor' => [], 'image' => []];
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model(Server::class, $data));
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

    public function test_it_retrieves_base_http_url()
    {
        $returnedUri = uri_for('http://foo.com');

        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn($returnedUri);

        $uri = $this->operator->testBaseUri();

        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertEquals($returnedUri, $uri);
    }

    /**
     * @expectedException \Exception
     */
    public function test_undefined_methods_result_in_error()
    {
        $this->operator->foo();
    }
}

class TestOperator extends Operator
{
    public function testBaseUri()
    {
        return $this->getHttpBaseUrl();
    }

    public function create($str)
    {
        return 'Created ' . $str;
    }

    public function fail()
    {
    }
}
