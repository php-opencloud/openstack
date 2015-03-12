<?php

namespace spec\OpenStack\Common\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\RequestInterface;
use OpenStack\Identity\v2\Models\Token;
use OpenStack\Identity\v2\Service as IdentityService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthHandlerSpec extends ObjectBehavior
{
    private $service;
    private $token;
    private $opts;

    function let(IdentityService $service, Token $token)
    {
        $this->service = $service;
        $this->token = $token;
        $this->opts = ['username' => 1, 'password' => 2, 'tenantName' => 3];

        $this->beConstructedWith($service, $this->opts, $token);
    }

    function it_should_bypass_auth_http_requests(BeforeEvent $event)
    {
        // Fake a Keystone request
        $request = new Request('POST', 'https://my-openstack.org:5000/v2.0/tokens');
        $event->getRequest()->willReturn($request);

        // since token calls themselves are the basis of authentication, it makes little
        // sense to run the auth hooks - so we should cancel the process early on.
        $this->checkTokenIsValid($event)->shouldReturn(null);
    }

    function it_should_generate_a_new_token_if_the_current_token_is_either_expired_or_not_set(BeforeEvent $event)
    {
        // force the mock token to indicate that its expired
        $this->token->hasExpired()->willReturn(true);

        $token = new Token(new Client());

        $this->service
            ->generateToken($this->opts['username'], $this->opts['password'], $this->opts['tenantName'], [])
            ->shouldBeCalled()
            ->willReturn($token);

        $event->getRequest()->willReturn(new Request('POST', ''));
        $this->checkTokenIsValid($event);
    }

    function it_should_set_the_header_of_all_requests_with_the_token_id(BeforeEvent $event, RequestInterface $request)
    {
        $this->token->id = 'RANDOM_TOKEN_ID';
        $this->token->hasExpired()->willReturn(false);

        $request->getUrl()->willReturn('');
        $request->setHeader('X-Auth-Token', 'RANDOM_TOKEN_ID')->shouldBeCalled();
        $event->getRequest()->willReturn($request);

        $this->checkTokenIsValid($event);
    }
}