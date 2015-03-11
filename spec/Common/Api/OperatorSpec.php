<?php

namespace spec\OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use OpenStack\Common\Api\Operator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OperatorSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beAnInstanceOf(__NAMESPACE__ . '\\TestOperator');
        $this->beConstructedWith($client);
    }

    function it_implements()
    {
        $this->shouldImplement('OpenStack\Common\Api\OperatorInterface');
    }

    function it_returns_operations()
    {
        $this->getOperation('fooOperation', [])->shouldReturnAnInstanceOf('OpenStack\Common\Api\Operation');
    }

    function it_throws_an_exception_when_no_operation_found()
    {
        $this->shouldThrow('\Exception')->duringGetOperation('blahOperation');
    }
}

class TestOperator extends Operator
{
    public function getApiClass()
    {
        return __NAMESPACE__ . '\\TestApi';
    }
}

class TestApi
{
    public static function fooOperation()
    {
        return [];
    }
}