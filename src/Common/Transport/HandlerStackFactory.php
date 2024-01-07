<?php

declare(strict_types=1);

namespace OpenStack\Common\Transport;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Utils;

class HandlerStackFactory
{
    public static function create(callable $handler = null): HandlerStack
    {
        $stack = new HandlerStack($handler ?: Utils::chooseHandler());
        $stack->push(Middleware::httpErrors(), 'http_errors');
        $stack->push(Middleware::prepareBody(), 'prepare_body');

        return $stack;
    }
}
