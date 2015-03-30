<?php

namespace spec\OpenStack\Common\Error;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotImplementedErrorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('OpenStack\Common\Error\NotImplementedError');
    }
}
