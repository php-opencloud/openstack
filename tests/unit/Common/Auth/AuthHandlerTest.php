<?php

namespace OpenStack\Test\Common\Auth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Message\Request;
use OpenStack\Common\Auth\AuthHandler;
use OpenStack\Identity\v3\Models\Token;
use OpenStack\Identity\v3\Service as IdentityService;
use Prophecy\PhpUnit\ProphecyTestCase;

class AuthHandlerTest extends ProphecyTestCase
{
    private $service;
    private $token;
    private $opts;
    private $handler;

    function setUp()
    {
        $this->service = $this->prophesize(IdentityService::class);
        $this->token = $this->prophesize(Token::class);
        $this->opts = ['username' => 1, 'password' => 2, 'tenantName' => 3];

        $this->handler = new AuthHandler($this->service->reveal(), $this->opts, $this->token->reveal());
    }

    public function test_it_should_bypass_auth_http_requests()
    {
        // Fake a Keystone request
        $request = new Request('POST', 'https://my-openstack.org:5000/v2.0/tokens');

        $event = $this->prophesize(BeforeEvent::class);
        $event->getRequest()->willReturn($request);

        // since token calls themselves are the basis of authentication, it makes little
        // sense to run the auth hooks - so we should cancel the process early on.
        $this->assertNull($this->handler->checkTokenIsValid($event->reveal()));
    }

    public function test_it_should_generate_a_new_token_if_the_current_token_is_either_expired_or_not_set()
    {
        // force the mock token to indicate that its expired
        $this->token->getId()->willReturn('');
        $this->token->hasExpired()->willReturn(true);

        $this->service->authenticate($this->opts)
            ->shouldBeCalled()
            ->willReturn([$this->token]);

        $event = $this->prophesize(BeforeEvent::class);
        $event->getRequest()->willReturn(new Request('POST', ''));

        $handler = new AuthHandler($this->service->reveal(), $this->opts, $this->token->reveal());
        $handler->checkTokenIsValid($event->reveal());
    }

    public function test_it_should_set_the_header_of_all_requests_with_the_token_id()
    {
        $this->token->getId()->willReturn('RANDOM_TOKEN_ID');
        $this->token->hasExpired()->willReturn(false);

        $request = $this->prophesize(Request::class);
        $request->getUrl()->willReturn('');
        $request->setHeader('X-Auth-Token', 'RANDOM_TOKEN_ID')->shouldBeCalled();

        $event = $this->prophesize(BeforeEvent::class);
        $event->getRequest()->willReturn($request);

        $this->handler->checkTokenIsValid($event->reveal());
    }
}