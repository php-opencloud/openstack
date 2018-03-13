<?php

declare(strict_types=1);

namespace OpenStack\Common\Auth;

use function GuzzleHttp\Psr7\modify_request;
use Psr\Http\Message\RequestInterface;

/**
 * This class is responsible for three tasks:.
 *
 * 1. performing the initial authentication for OpenStack services
 * 2. populating the ``X-Auth-Token`` header for every HTTP request
 * 3. checking the token expiry before each request, and re-authenticating if necessary
 */
class AuthHandler
{
    /** @var callable */
    private $nextHandler;

    /** @var callable */
    private $tokenGenerator;

    /** @var Token */
    private $token;

    /**
     * @param callable $nextHandler
     * @param callable $tokenGenerator
     */
    public function __construct(callable $nextHandler, callable $tokenGenerator, Token $token = null)
    {
        $this->nextHandler    = $nextHandler;
        $this->tokenGenerator = $tokenGenerator;
        $this->token          = $token;
    }

    /**
     * This method is invoked before every HTTP request is sent to the API. When this happens, it
     * checks to see whether a token is set and valid, and then sets the ``X-Auth-Token`` header
     * for the HTTP request before letting it continue on its merry way.
     *
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return mixed|void
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        if ($this->shouldIgnore($request)) {
            return $fn($request, $options);
        }

        if (!$this->token || $this->token->hasExpired()) {
            $this->token = call_user_func($this->tokenGenerator);
        }

        $modify = ['set_headers' => ['X-Auth-Token' => $this->token->getId()]];

        return $fn(modify_request($request, $modify), $options);
    }

    /**
     * Internal method which prevents infinite recursion. For certain requests, like the initial
     * auth call itself, we do NOT want to send a token.
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function shouldIgnore(RequestInterface $request): bool
    {
        return false !== strpos((string) $request->getUri(), 'tokens') && 'POST' == $request->getMethod();
    }
}
