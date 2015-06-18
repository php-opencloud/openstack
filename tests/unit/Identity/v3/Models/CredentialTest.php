<?php

namespace OpenStack\Test\Identity\v3\Models;

use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Credential;
use OpenStack\Test\TestCase;

class CredentialTest extends TestCase
{
    private $credential;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->credential = new Credential($this->client->reveal(), new Api());
    }

    public function test_it_retrieves()
    {

    }
}