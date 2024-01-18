<?php

namespace OpenStack\Common\Transport;

/**
 * @deprecated use \OpenStack\Common\Transport\HandlerStackFactory instead
 */
class HandlerStack
{
    public static function create(callable $handler = null): \GuzzleHttp\HandlerStack
    {
        return HandlerStackFactory::create($handler);
    }
}
