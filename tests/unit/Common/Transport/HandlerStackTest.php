<?php

namespace OpenStack\Test\Common\Transport;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use OpenStack\Common\Transport\HandlerStackFactory;
use OpenStack\Test\TestCase;

class HandlerStackTest extends TestCase
{
    public function test_it_is_created()
    {
        self::assertInstanceOf(HandlerStack::class, HandlerStackFactory::create(new MockHandler()));
    }
}
