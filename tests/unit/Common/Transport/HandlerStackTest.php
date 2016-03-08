<?php

namespace OpenCloud\Test\Common\Transport;

use GuzzleHttp\Handler\MockHandler;
use OpenCloud\Common\Transport\HandlerStack;
use OpenCloud\Test\TestCase;

class HandlerStackTest extends TestCase
{
    public function test_it_is_created()
    {
        $this->assertInstanceOf(HandlerStack::class, HandlerStack::create(new MockHandler()));
    }
}
