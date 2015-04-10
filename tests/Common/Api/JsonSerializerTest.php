<?php

namespace OpenStack\Test\Common\Api;

use OpenStack\Common\Api\JsonSerializer;
use OpenStack\Common\Api\Operation;
use OpenStack\Identity\v2\Api\Token as TokenApi;
use OpenStack\Compute\v2\Api as ComputeV2Api;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    public function setUp()
    {
        $this->serializer = new JsonSerializer();
    }

    public function test_it_embeds_params_according_to_path()
    {
        $params = Operation::toParamArray(TokenApi::post()['params']);

        $userValue = ['username' => 'foo', 'password' => 'bar', 'tenantId' => 'blah'];

        $expected = [
            'auth' => [
                'passwordCredentials' => [
                    'username' => 'foo',
                    'password' => 'bar',
                ],
                'tenantId' => 'blah',
            ],
        ];

        $this->assertEquals($expected, $this->serializer->serialize($userValue, $params));
    }

    public function test_it_nests_json_objects_if_a_top_level_key_is_provided()
    {
        $params = Operation::toParamArray(ComputeV2Api::postServer()['params']);

        $userValue = ['name' => 'foo', 'imageId' => 'bar', 'flavorId' => 'baz'];

        $expected = [
            'server' => [
                'name' => $userValue['name'],
                'imageRef' => $userValue['imageId'],
                'flavorRef' => $userValue['flavorId'],
            ]
        ];

        $this->assertEquals($expected, $this->serializer->serialize($userValue, $params, ['jsonKey' => 'server']));
    }
}