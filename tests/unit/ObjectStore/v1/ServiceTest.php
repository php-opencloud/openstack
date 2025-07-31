<?php

namespace OpenStack\Test\ObjectStore\v1;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\ObjectStore\v1\Service;
use OpenStack\Test\TestCase;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_Account()
    {
        self::assertInstanceOf(Account::class, $this->service->getAccount());
    }

    public function test_it_lists_containers()
    {
        $this->mockRequest('GET', ['query' => ['limit' => 2, 'format' => 'json']], 'GET_Container');

        foreach ($this->service->listContainers(['limit' => 2]) as $container) {
            self::assertInstanceOf(Container::class, $container);
        }
    }

    public function test_It_Create_Containers()
    {
        $this->mockRequest('PUT', 'foo', 'Created', null, []);
        $this->service->createContainer(['name' => 'foo']);
    }

    public function test_it_returns_true_for_existing_containers()
    {
        $this->mockRequest('HEAD', 'foo', new Response(200), null, []);

        self::assertTrue($this->service->containerExists('foo'));
    }

    public function test_it_returns_false_if_container_does_not_exist()
    {
        $e = new BadResponseError();
        $e->setRequest(new Request('HEAD', 'foo'));
        $e->setResponse(new Response(404));

        $this->mockRequest('HEAD', 'foo', $e);

        self::assertFalse($this->service->containerExists('foo'));
    }

    public function test_it_throws_exception_when_error()
    {
        $e = new BadResponseError();
        $e->setRequest(new Request('HEAD', 'foo'));
        $e->setResponse(new Response(500));

        $this->mockRequest('HEAD', 'foo', $e);

		$this->expectException(BadResponseError::class);

        $this->service->containerExists('foo');
    }
}
