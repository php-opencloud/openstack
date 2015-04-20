<?php

namespace OpenStack\Test\Common\Auth;

use GuzzleHttp\Client;
use OpenStack\Common\Auth\ServiceUrlResolver;
use Prophecy\PhpUnit\ProphecyTestCase;

class ServiceUrlResolverTest extends ProphecyTestCase
{
    private $resolver;
    private $client;

    public function setUp()
    {
        $this->client = $this->prophesize(Client::class);
        $this->resolver = new ServiceUrlResolver($this->client->reveal());
    }

    public function test_it_resolves()
    {

    }
}