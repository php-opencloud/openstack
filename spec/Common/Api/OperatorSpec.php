<?php

namespace spec\OpenStack\Common\Api;

use GuzzleHttp\Client;
use OpenStack\Common\Api\Operator;
use OpenStack\Compute\v2\Api as ComputeV2Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OperatorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(__NAMESPACE__ . '\\TestOperator');
        $this->beConstructedWith(new Client());
    }

    function it_implements()
    {
        $this->shouldImplement('OpenStack\Common\Api\OperatorInterface');
    }

    function it_returns_operations()
    {
        $this->getOperation(ComputeV2Api::postServer(), [])->shouldReturnAnInstanceOf('OpenStack\Common\Api\Operation');
    }
}

class TestOperator extends Operator
{
    public function getServiceNamespace()
    {}
}