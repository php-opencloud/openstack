<?php

namespace spec\OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use OpenStack\Compute\v2\Api as ComputeV2Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OperationSpec extends ObjectBehavior
{
    private $definition;
    private $client;

    function let(ClientInterface $client)
    {
        $this->definition = ComputeV2Api::postServer();
        $this->client = $client;

        $this->beConstructedWith($client, $this->definition, []);
    }

    function it_throws_exception_when_user_does_not_provide_required_options()
    {
        $this->shouldThrow('\Exception')->duringValidate([]);
    }

    function it_throws_exception_when_user_provides_undefined_options()
    {
        $userData = ['name' => 'new_server', 'undefined_opt' => 'bah'];
        $this->beConstructedWith($this->client, $this->definition, $userData);

        $this->shouldThrow('\Exception')->duringValidate();
    }
}