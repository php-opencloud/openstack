<?php

namespace spec\OpenStack\Common\Api;

use OpenStack\Common\Api\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use OpenStack\Identity\v2\Api\Token as TokenApi;
use OpenStack\Compute\v2\Api as ComputeV2Api;

class JsonSerializerSpec extends ObjectBehavior
{
    function it_embeds_params_according_to_path()
    {
        $params = Operation::toParamArray(TokenApi::post()['params']);

        $userValue = ['username' => 'foo', 'password' => 'bar', 'tenantId' => 'blah'];

        $expectedStructure = [
            'auth' => [
                'passwordCredentials' => [
                    'username' => 'foo',
                    'password' => 'bar',
                ],
                'tenantId' => 'blah',
            ],
        ];

        $this->serialize($userValue, $params)->shouldReturn($expectedStructure);
    }

    function it_nests_json_objects_if_a_top_level_key_is_provided()
    {
        $params = Operation::toParamArray(ComputeV2Api::postServer()['params']);

        $userValue = ['name' => 'foo', 'imageId' => 'bar', 'flavorId' => 'baz'];

        $expectedStructure = [
            'server' => [
                'name' => $userValue['name'],
                'imageRef' => $userValue['imageId'],
                'flavorRef' => $userValue['flavorId'],
            ]
        ];

        $this->serialize($userValue, $params, ['jsonKey' => 'server'])->shouldReturn($expectedStructure);
    }
}