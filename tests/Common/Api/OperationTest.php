<?php

namespace OpenStack\Test\Common\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Request;
use OpenStack\Common\Api\Operation;
use OpenStack\Compute\v2\Api as ComputeV2Api;

class OperationTest extends \PHPUnit_Framework_TestCase
{
    private $operation;

    function setUp()
    {
        $def = ComputeV2Api::postServer();

        $this->operation = new Operation(new Client(), $def);
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

    public function test_headers_are_set_on_request()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->createRequest('POST', 'path', [
            'headers' => ['X-Foo' => 'bar']
        ])->shouldBeCalled();

        $def = [
            'method' => 'POST',
            'path' => 'path',
            'params' => [
                'Foo' => ['type' => 'string', 'location' => 'header', 'sentAs' => 'X-Foo']
            ]
        ];

        $userVals = ['Foo' => 'bar'];

        $operation = new Operation($client->reveal(), $def, $userVals);
        $operation->createRequest();
    }

    public function test_json_is_set_on_request()
    {
        $client = $this->prophesize(ClientInterface::class);
        $client->createRequest('POST', 'path', [
            'json' => ['X-Foo' => 'bar']
        ])->shouldBeCalled();

        $def = [
            'method' => 'POST',
            'path' => 'path',
            'params' => [
                'Foo' => ['type' => 'string', 'location' => 'json', 'sentAs' => 'X-Foo']
            ]
        ];

        $userVals = ['Foo' => 'bar'];

        $operation = new Operation($client->reveal(), $def, $userVals);
        $operation->createRequest();
    }
}