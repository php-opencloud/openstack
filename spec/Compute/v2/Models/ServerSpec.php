<?php

namespace spec\OpenStack\Compute\v2\Models;

use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ServerSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldImplement('OpenStack\Common\Resource\ResourceInterface');
    }
}
