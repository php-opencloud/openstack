<?php

namespace OpenStack\Test\Common\Api;

use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Parameter;
use OpenStack\Test\Fixtures\ComputeV2Api;

class OperationTest extends \PHPUnit\Framework\TestCase
{
    private $operation;

    public function setUp()
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

    public function test_it_validates_params()
    {
        $this->assertTrue($this->operation->validate([
            'name'     => 'foo',
            'imageId'  => 'bar',
            'flavorId' => 'baz',
        ]));
    }

    /**
     * @expectedException \Exception
     */
    public function test_exceptions_are_propagated()
    {
        $this->assertFalse($this->operation->validate([
            'name'     => true,
            'imageId'  => 'bar',
            'flavorId' => 'baz',
        ]));
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

    public function test_it_gets_json_key()
    {
        $this->assertEquals('server', $this->operation->getJsonKey());
    }
}
