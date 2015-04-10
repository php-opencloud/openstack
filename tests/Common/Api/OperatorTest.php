<?php

namespace OpenStack\Test\Common\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Event\Emitter;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use OpenStack\Common\Api\Operator;
use OpenStack\Common\Resource\ResourceInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class OperatorTest extends ProphecyTestCase
{
    private $operator;
    private $client;
    private $def;

    function setUp()
    {
        $this->client = $this->prophesize(Client::class);
        $this->client->getEmitter()->willReturn(new Emitter());

        $this->def = [
            'method' => 'GET',
            'path'   => 'test',
            'params' => [],
        ];

        $this->operator = new TestOperator($this->client->reveal());
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
        $this->client->createRequest('GET', 'test', [])->willReturn(new Request('GET', 'test'));
        $this->client->send(Argument::type(Request::class))->shouldBeCalled();

        $this->operator->execute($this->def, []);
    }

    public function test_it_returns_a_model_instance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model('Server'));
    }

    public function test_it_populates_models_from_response()
    {
        $response = new Response(200);
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model('Server', $response));
    }

    public function test_it_populates_models_from_arrays()
    {
        $data = ['flavor' => [], 'image' => []];
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model('Server', $data));
    }
}

class TestOperator extends Operator
{
    public function getServiceNamespace()
    {
        return 'OpenStack\Compute\v2';
    }

    public function model($name, $data = null)
    {
        return parent::model($name, $data);
    }
}