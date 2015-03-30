<?php

namespace spec\OpenStack\Compute\v2\Models;

use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Client());
    }

    function it_is_initializable()
    {
        $this->shouldImplement('OpenStack\Common\Resource\ResourceInterface');
    }
}
