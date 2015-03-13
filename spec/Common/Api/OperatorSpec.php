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
        $this->getOperation([], [])->shouldReturnAnInstanceOf('OpenStack\Common\Api\Operation');
    }
}

class TestOperator extends Operator
{
}