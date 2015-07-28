<?php

namespace OpenStack\Test\ObjectStore\v1;

use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\ObjectStore\v1\Models\Container;
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

    public function test_it_lists_containers()
    {
        $req = $this->setupMockRequest('GET', '?limit=2&format=json');
        $this->setupMockResponse($req, 'GET_Container');

        foreach ($this->service->listContainers(['limit' => 2]) as $container) {
            $this->assertInstanceOf(Container::class, $container);
        }
    }

    public function test_It_Create_Containers()
    {
        $this->setupMockResponse($this->setupMockRequest('PUT', 'foo'), 'Created');
        $this->service->createContainer(['name' => 'foo']);
    }
}