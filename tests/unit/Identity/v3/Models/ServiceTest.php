<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Service;
use OpenStack\Test\TestCase;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->service = new Service($this->client->reveal(), new Api());
        $this->service->id = 'SERVICE_ID';
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'services/SERVICE_ID');
        $this->setupMockResponse($request, 'service');

        $this->service->retrieve();
    }

    public function test_it_updates()
    {
        $this->service->type = 'foo';

        $request = $this->setupMockRequest('PATCH', 'services/SERVICE_ID', ['type' => 'foo']);
        $this->setupMockResponse($request, 'service');

        $this->service->update();
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'services/SERVICE_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->service->delete();
    }
} 