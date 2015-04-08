<?php

namespace spec\OpenStack\Compute\v2\Models;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KeypairSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Client());
    }
}
