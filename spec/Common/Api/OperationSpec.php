<?php

namespace spec\OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\RequestInterface;
use OpenStack\Compute\v2\Api as ComputeV2Api;
use OpenStack\Identity\v2\Api as IdentityV2Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OperationSpec extends ObjectBehavior
{
    private $parameters;
    private $client;

    function let(ClientInterface $client)
    {
        $this->parameters = ComputeV2Api::postServers();
        $this->client = $client;

        $this->beConstructedWith($client, $this->parameters, []);
    }

    function it_throws_exception_when_user_does_not_provide_required_options()
    {
        $this->shouldThrow('\Exception')->duringValidate([]);
    }

    function it_throws_exception_when_user_provides_undefined_options()
    {
        $userData = ['name' => 'new_server', 'undefined_opt' => 'bah'];
        $this->beConstructedWith($this->client, $this->parameters, $userData);

        $this->shouldThrow('\Exception')->duringValidate();
    }

    function it_should_return_true_when_required_attributes_are_provided()
    {
        $userData = ['name' => '1', 'imageId' => '2', 'flavorId' => '3'];
        $this->beConstructedWith($this->client, $this->parameters, $userData);

        $this->validate()->shouldReturn(true);
    }

    function it_throws_exception_when_values_do_not_match_defined_types()
    {
        $userData = ['name' => '1', 'imageId' => '2', 'flavorId' => '3', 'networks' => 'a_network!'];
        $this->beConstructedWith($this->client, $this->parameters, $userData);

        $this->shouldThrow('\Exception')->duringValidate();
    }

    function it_throws_exception_when_deeply_nested_values_have_wrong_types()
    {
        $networks = [
            ['name' => false] // name should be a string
        ];
        $userData = ['name' => '1', 'imageId' => '2', 'flavorId' => '3', 'networks' => $networks];
        $this->beConstructedWith($this->client, $this->parameters, $userData);

        $this->shouldThrow('\Exception')->duringValidate();
    }

    function it_stocks_headers_of_request(RequestInterface $request)
    {
        $definition = include 'fixtures/headers.php';

        $userData = ['name' => 'john_doe', 'age' => 30, 'metadata' => ['hair_color' => 'brown'], 'other' => 'blah'];

        $this->client->createRequest($definition['method'], $definition['path'], [
            'json' => ['other' => $userData['other']],
            'headers' => [
                'X-Foo-Name' => $userData['name'],
                'age' => $userData['age'],
                'X-Meta-hair_color' => $userData['metadata']['hair_color'],
            ]
        ])->shouldBeCalled();

        $this->beConstructedWith($this->client, $definition, $userData);
        $this->createRequest($request);
    }

    function it_stocks_json_body_of_request()
    {
        $definition = include 'fixtures/jsonBody.php';

        $userData = [
            'name'  => 'MY_NAME',
            'other' => ['elem1', 'elem2', 'elem3'],
            'etc'   => ['dob' => '01.01.1900', 'age' => 115]
        ];

        $expected = [
            'server_name'  => $userData['name'],
            'other_params' => $userData['other'],
            'etcetc'       => ['dob' => $userData['etc']['dob'], 'current_age' => $userData['etc']['age']]
        ];

        $this->client->createRequest($definition['method'], $definition['path'], ['json' => $expected])->shouldBeCalled();

        $this->beConstructedWith($this->client, $definition, $userData);
        $this->createRequest();
    }

    function it_embeds_params_according_to_path()
    {
        $definition = IdentityV2Api::postTokens();
        $userData = ['username' => 'foo', 'password' => 'bar', 'tenantId' => 'blah'];
        $this->beConstructedWith($this->client, $definition, $userData);

        $expectedStructure = [
            'auth' => [
                'passwordCredentials' => [
                    'username' => 'foo',
                    'password' => 'bar',
                ],
                'tenantId' => 'blah',
            ],
        ];

        $this->client->createRequest($definition['method'], $definition['path'], [
            'json' => $expectedStructure
        ])->shouldBeCalled();
        $this->createRequest();
    }

    function it_nests_json_objects_if_a_key_is_provided()
    {
        $userData = ['name' => 'foo', 'imageId' => 'bar', 'flavorId' => 'baz'];

        $expectedStructure = [
            'server' => [
                'name' => $userData['name'],
                'imageRef' => $userData['imageId'],
                'flavorRef' => $userData['flavorId'],
            ]
        ];

        $this->client->createRequest($this->parameters['method'], $this->parameters['path'], [
            'json' => $expectedStructure
        ])->shouldBeCalled();

        $this->beConstructedWith($this->client, $this->parameters, $userData);
        $this->createRequest();
    }
}