<?php

namespace OpenStack\Test\Identity\v2;

use OpenStack\Identity\v2\Api;
use OpenStack\Identity\v2\Models\Token;
use OpenStack\Identity\v2\Service;
use OpenStack\Test\TestCase;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }
    
    public function test_it_generates_tokens()
    {
        $options = [
            'username' => 'foo',
            'password' => 'bar',
            'tenantId' => 'baz',
        ];

        $expectedJson = ['auth' => [
            'passwordCredentials' => [
                'username' => $options['username'],
                'password' => $options['password'],
            ],
            'tenantId' => $options['tenantId'],
        ]];

        $this->setupMock('POST', 'tokens', $expectedJson, [], 'token-post');

        $this->assertInstanceOf(Token::class, $this->service->generateToken($options));
    }
}