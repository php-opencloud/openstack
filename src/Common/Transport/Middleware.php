<?php

namespace OpenStack\Common\Transport;

use function GuzzleHttp\Psr7\modify_request;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware as GuzzleMiddleware;
use OpenStack\Common\Auth\AuthHandler;
use OpenStack\Common\Auth\Token;
use OpenStack\Common\Error\Builder;
use Psr\Http\Message\ResponseInterface;

final class Middleware
{
    /**
     * @return callable
     */
    public static function httpErrors()
    {
        return function (callable $handler) {
            return function ($request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $handler) {
                        if ($response->getStatusCode() < 400) {
                            return $response;
                        }
                        throw (new Builder())->httpError($request, $response);
                    }
                );
            };
        };
    }

    /**
     * @param callable $tokenGenerator
     * @param Token    $token
     *
     * @return callable
     */
    public static function authHandler(callable $tokenGenerator, Token $token = null)
    {
        return function (callable $handler) use ($tokenGenerator, $token) {
            return new AuthHandler($handler, $tokenGenerator, $token);
        };
    }

    /**
     * @codeCoverageIgnore
     */
    public static function history(array &$container)
    {
        return GuzzleMiddleware::history($container);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function retry(callable $decider, callable $delay = null)
    {
        return GuzzleMiddleware::retry($decider, $delay);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function log(LoggerInterface $logger, MessageFormatter $formatter, $logLevel = LogLevel::INFO)
    {
        return GuzzleMiddleware::log($logger, $formatter, $logLevel);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function prepareBody()
    {
        return GuzzleMiddleware::prepareBody();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function mapRequest(callable $fn)
    {
        return GuzzleMiddleware::mapRequest($fn);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function mapResponse(callable $fn)
    {
        return GuzzleMiddleware::mapResponse($fn);
    }
}