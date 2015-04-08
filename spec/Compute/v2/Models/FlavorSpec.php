<?php

namespace spec\OpenStack\Compute\v2\Models;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FlavorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Client());
    }
}
