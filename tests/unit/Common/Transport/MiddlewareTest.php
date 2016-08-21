<?php

namespace unit\Common\Transport;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Transport\Middleware;
use OpenStack\Test\TestCase;
use OpenStack\Common\Auth\AuthHandler;

class MiddlewareTest extends TestCase
{
    /**
     * @expectedException \OpenStack\Common\Error\BadResponseError
     */
    public function test_exception_is_thrown_for_4xx_statuses()
    {
        $middleware = Middleware::httpErrors();

        $handler = new MockHandler([new Response(404)]);
        $fn = $middleware($handler);

        $promise = $fn(new Request('GET', 'http://foo.com'), []);
        $this->assertEquals('pending', $promise->getState());

        $promise->wait();
        $this->assertEquals('rejected', $promise->getState());
    }

    public function test_responses_are_left_alone_when_status_under_400()
    {
        $middleware = Middleware::httpErrors();

        $response = new Response(204);
        $handler  = new MockHandler([$response]);
        $fn = $middleware($handler);

        $promise = $fn(new Request('GET', 'http://foo.com'), []);

        $promise->then(function ($val) use ($response) {
            $this->assertEquals($val, $response);
        });

        $promise->wait();
    }

    public function test_auth_handler_is_returned()
    {
        $generator = function () {
        };

        $middleware = Middleware::authHandler($generator);

        $handler  = new MockHandler([new Response(204)]);
        $fn = $middleware($handler);

        $this->assertInstanceOf(AuthHandler::class, $fn);
    }
}
