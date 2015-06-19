<?php

namespace OpenStack\Common\Auth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;

/**
 * This class is responsible for three tasks:
 *
 * 1. performing the initial authentication for OpenStack services
 * 2. populating the ``X-Auth-Token`` header for every HTTP request
 * 3. checking the token expiry before each request, and re-authenticating if necessary
 */
class AuthHandler implements SubscriberInterface
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
     * @var IdentityService
     */
    private $service;

    /**
     * Configuration options
     *
     * @var array
     */
    private $options;

    /**
     * @param IdentityService $service
     * @param array           $options
     * @param Token           $token
     */
    public function __construct(IdentityService $service, array $options, Token $token)
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

        $request->setHeader('X-Auth-Token', $this->token->getId());
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
        return strpos((string) $request->getUrl(), 'tokens') !== false && $request->getMethod() == 'POST';
    }

    /**
     * Authenticates and retrieves a fresh token for caching.
     */
    public function authenticate()
    {
        list ($this->token,) = $this->service->authenticate($this->options);
    }
}