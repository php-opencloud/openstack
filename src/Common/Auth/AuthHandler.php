<?php

namespace OpenStack\Common\Auth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Message\RequestInterface;
use OpenStack\Identity\v2\Models\Token;
use OpenStack\Identity\v2\Service as IdentityV2Service;

/**
 * This class is responsible for three tasks:
 *
 * 1. performing the initial authentication for OpenStack services
 * 2. populating the ``X-Auth-Token`` header for every HTTP request
 * 3. checking the token expiry before each request, and re-authenticating if necessary
 *
 * This handler is specific to OpenStack KeyStone v2 and uses it as the default authentication strategy.
 */
class AuthHandler implements AuthHandlerInterface
{
    /**
     * The cached token
     *
     * @var Token
     */
    private $token;

    /**
     * The service responsible for handling the authentication operation
     *
     * @var IdentityV2Service
     */
    private $service;

    /**
     * Configuration options
     *
     * @var array
     */
    private $options;

    /**
     * @param IdentityV2Service $service
     * @param array             $options
     * @param Token             $token
     */
    public function __construct(IdentityV2Service $service, array $options, Token $token)
    {
        $this->service = $service;
        $this->options = $options;
        $this->token   = $token;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function getEvents()
    {
        return [
            'before' => ['checkTokenIsValid']
        ];
    }

    /**
     * This method is invoked before every HTTP request is sent to the API. When this happens, it
     * checks to see whether a token is set and valid, and then sets the ``X-Auth-Token`` header
     * for the HTTP request before letting it continue on its merry way.
     *
     * @param BeforeEvent $event
     *
     * @return mixed|void
     */
    public function checkTokenIsValid(BeforeEvent $event)
    {
        $request = $event->getRequest();

        if ($this->shouldIgnore($request)) {
            return;
        }

        if (!$this->token || $this->token->hasExpired()) {
            $this->authenticate();
        }

        $request->setHeader('X-Auth-Token', $this->token->id);
    }

    /**
     * Internal method which prevents infinite recursion. For certain requests, like the initial
     * auth call itself, we do NOT want to send a token.
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function shouldIgnore(RequestInterface $request)
    {
        return strpos((string) $request->getUrl(), 'tokens') !== false;
    }

    /**
     * Authenticates and retrieves a fresh token for caching.
     */
    public function authenticate()
    {
        $this->token = $this->service->generateToken($this->options);
    }
}