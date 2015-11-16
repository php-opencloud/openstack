<?php

namespace OpenStack\Test\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Api\Operator;
use OpenStack\Common\Resource\ResourceInterface;
use OpenStack\Test\Fixtures\ComputeV2Api;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Psr\Http\Message\RequestInterface;

class OperatorTest extends ProphecyTestCase
{
    private $operator;
    private $client;
    private $def;

    function setUp()
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

    public function test_it_returns_a_model_instance()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model('Server'));
    }

    public function test_it_populates_models_from_response()
    {
        $this->assertInstanceOf(ResourceInterface::class, $this->operator->model('Server', new Response(200)));
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