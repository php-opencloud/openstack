<?php

namespace OpenStack\Test\Common\Transport;

use GuzzleHttp\Handler\MockHandler;
use OpenStack\Common\Transport\HandlerStack;
use OpenStack\Test\TestCase;

class HandlerStackTest extends TestCase
{
    public function test_it_is_created()
    {
        $this->assertInstanceOf(HandlerStack::class, HandlerStack::create(new MockHandler()));
    }
}
