<?php

namespace OpenStack\Test\ObjectStore\v1\Models;

use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\Test\TestCase;

class AccountTest extends TestCase
{
    private $account;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->account = new Account($this->client->reveal(), new Api());
    }

    public function test_Response_Populates_Model()
    {
        $response = $this->getFixture('HEAD_Account');

        $this->account->populateFromResponse($response);

        $this->assertEquals(1, $this->account->objectCount);
        $this->assertEquals(['Book' => 'MobyDick', 'Genre' => 'Fiction'], $this->account->metadata);
        $this->assertEquals(14, $this->account->bytesUsed);
        $this->assertEquals(2, $this->account->containerCount);
    }

    public function test_Retrieve()
    {
        $this->setupMockResponse($this->setupMockRequest('HEAD', ''), 'HEAD_Account');

        $this->account->retrieve();

        $this->assertNotEmpty($this->account->metadata);
    }
}