<?php

namespace OpenStack\Test\Common\Api;

use GuzzleHttp\Client;
use OpenStack\Common\Api\Operation;
use OpenStack\Compute\v2\Api as ComputeV2Api;

class OperationTest extends \PHPUnit_Framework_TestCase
{
    private $operation;

    function setUp()
    {
        $this->operation = new Operation(new Client(), ComputeV2Api::postServer());
    }

    /**
     * @expectedException \Exception
     */
    public function test_an_exception_is_thrown_when_user_does_not_provide_required_options()
    {
        $this->operation->validate([]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_when_user_provides_undefined_options()
    {
        $userData = ['name' => 'new_server', 'undefined_opt' => 'bah'];

        $this->operation->validate($userData);
    }
}