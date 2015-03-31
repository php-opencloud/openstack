<?php

namespace spec\OpenStack\Identity\v2;

use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServiceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Client());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('OpenStack\Identity\v2\Service');
    }
}