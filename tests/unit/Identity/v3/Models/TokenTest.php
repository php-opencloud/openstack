<?php

namespace OpenStack\Test\Identity\v3\Models;

use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Token;
use OpenStack\Test\TestCase;

class TokenTest extends TestCase
{
    private $token;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->token = new Token($this->client->reveal(), new Api());
        $this->token->id = 'TOKEN_ID';
    }

    public function test_it_retrieves()
    {

    }
} 