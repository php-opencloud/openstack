<?php

namespace spec\OpenStack\ObjectStore\v2;

use GuzzleHttp\ClientInterface;
use OpenStack\Common\ApiInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('OpenStack\Common\Api\Operator');
    }
}