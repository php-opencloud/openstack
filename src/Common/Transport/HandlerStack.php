<?php

declare(strict_types=1);

namespace OpenStack\Common\Transport;

use function GuzzleHttp\choose_handler;
use GuzzleHttp\HandlerStack as GuzzleStack;

class HandlerStack extends GuzzleStack
{
    public static function create(callable $handler = null): self
    {
        $stack = new self($handler ?: choose_handler());

        $stack->push(Middleware::httpErrors());
        $stack->push(Middleware::prepareBody());

        return $stack;
    }
}
