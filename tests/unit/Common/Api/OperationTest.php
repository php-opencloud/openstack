<?php

namespace OpenStack\Test\Common\Api;

use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Parameter;
use OpenStack\Test\Fixtures\ComputeV2Api;

class OperationTest extends \PHPUnit_Framework_TestCase
{
    private $operation;

    function setUp()
    {
        $def = (new ComputeV2Api())->postServer();

        $this->operation = new Operation($def);
    }


    public function test_it_reveals_whether_params_are_set_or_not()
    {
        $this->assertFalse($this->operation->hasParam('foo'));
        $this->assertTrue($this->operation->hasParam('name'));
    }

    public function test_it_gets_params()
    {
        $this->assertInstanceOf(Parameter::class, $this->operation->getParam('name'));
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