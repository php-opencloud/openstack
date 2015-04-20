<?php

namespace OpenStack\Test\Common\Api;

use OpenStack\Common\Api\JsonSerializer;
use OpenStack\Common\Api\Operation;
use OpenStack\Test\Fixtures\ComputeV2Api;
use OpenStack\Test\Fixtures\IdentityV2Api;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;
    private $identityApi;
    private $computeApi;

    public function setUp()
    {
        $this->computeApi  = new ComputeV2Api();
        $this->identityApi = new IdentityV2Api();

        $this->serializer = new JsonSerializer();
    }

    public function test_it_embeds_params_according_to_path()
    {
        $params = Operation::toParamArray($this->identityApi->postToken()['params']);

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
        $params = Operation::toParamArray($this->computeApi->postServer()['params']);

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

    public function test_it_nests_json_arrays()
    {
        $params = Operation::toParamArray($this->computeApi->postServer()['params']);

        $userValues = [
            'securityGroups' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ]
        ];

        $expected = [
            'security_groups' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ]
        ];

        $this->assertEquals($expected, $this->serializer->serialize($userValues, $params));
    }

    public function test_it_nests_shallow_arrays()
    {
        $params = Operation::toParamArray(['foo' => ['type' => 'array', 'items' => ['type' => 'string']]]);

        $userValues = ['foo' => ['1', '2', '3']];

        $expected = $userValues;

        $this->assertEquals($expected, $this->serializer->serialize($userValues, $params));
    }

    public function test_it_nests_shallow_objects()
    {
        $params = Operation::toParamArray(['foo' => ['type' => 'object', 'properties' => ['bar' => ['type' => 'string']]]]);

        $userValues = ['foo' => ['bar' => 'hi']];

        $expected = $userValues;

        $this->assertEquals($expected, $this->serializer->serialize($userValues, $params));
    }

    public function test_it_ignores_non_json_locations()
    {
        $params = Operation::toParamArray(['foo' => ['type' => 'string', 'location' => 'header']]);

        $userValues = ['foo' => 'bar'];
        $expected = [];

        $this->assertEquals($expected, $this->serializer->serialize($userValues, $params));
    }
}