<?php

namespace OpenStack\Common\Auth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Message\RequestInterface;
use OpenStack\Identity\v2\Models\Token;
use OpenStack\Identity\v2\Service as IdentityV2Service;

/**
 * The Auth Handler for the OpenStack provider.
 */
class AuthHandler implements AuthHandlerInterface
{
    private $token;
    private $service;
    private $options;

    public function __construct(IdentityV2Service $service, array $options, Token $token)
    {
        $this->service = $service;
        $this->options = $options;
        $this->token   = $token;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getEvents()
    {
        return [
            'before' => ['checkTokenIsValid']
        ];
    }

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

    private function shouldIgnore(RequestInterface $request)
    {
        return strpos((string) $request->getUrl(), 'tokens') !== false;
    }

    public function authenticate()
    {
        $username = $this->options['username'];
        $password = $this->options['password'];
        $tenantName = isset($this->options['tenantName']) ? $this->options['tenantName'] : '';

        $remaining = array_diff_key($this->options, ['username' => null, 'password' => null, 'tenantName' => null]);
        $this->token = $this->service->generateToken($username, $password, $tenantName, $remaining);
    }
}