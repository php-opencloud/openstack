<?php

namespace OpenStack\Common\Transport;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware as GuzzleMiddleware;

final class Middleware
{
    public static function httpErrors()
    {

    }

    public static function history(array &$container)
    {
        return GuzzleMiddleware::history($container);
    }

    public static function retry(callable $decider, callable $delay = null)
    {
        return GuzzleMiddleware::retry($decider, $delay);
    }

    public static function log(LoggerInterface $logger, MessageFormatter $formatter, $logLevel = LogLevel::INFO)
    {
        return GuzzleMiddleware::log($logger, $formatter, $logLevel);
    }

    public static function prepareBody()
    {
        return GuzzleMiddleware::prepareBody();
    }

    public static function mapRequest(callable $fn)
    {
        return GuzzleMiddleware::mapRequest($fn);
    }

    public static function mapResponse(callable $fn)
    {
        return GuzzleMiddleware::mapResponse($fn);
    }
}