<?php

namespace OpenStack\Test\ObjectStore\v1;

use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\ObjectStore\v1\Service;
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

    public function test_Account()
    {
        $this->assertInstanceOf(Account::class, $this->service->getAccount());
    }
}