<?php

namespace OpenStack\Test\Common\Api;

use GuzzleHttp\Client;
use OpenStack\Common\Api\Operator;
use OpenStack\Compute\v2\Api as ComputeV2Api;

class OperatorTest extends \PHPUnit_Framework_TestCase
{
    private $operator;

    function setUp()
    {
        $this->operator = new TestOperator(new Client());
    }

    public function test_it_returns_operations()
    {
        $this->assertInstanceOf(
            'OpenStack\Common\Api\Operation',
            $this->operator->getOperation(ComputeV2Api::postServer(), [])
        );
    }
}

class TestOperator extends Operator
{
    public function getServiceNamespace()
    {}
}