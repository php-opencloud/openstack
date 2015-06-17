<?php

namespace OpenStack\Test\Identity\v3;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Endpoint;
use OpenStack\Identity\v3\Service;
use OpenStack\Test\TestCase;

class EndpointTest extends TestCase
{
    private $endpoint;
    private $service;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->service  = new Service($this->client->reveal(), new Api());

        $this->endpoint = new Endpoint($this->client->reveal(), new Api());
        $this->endpoint->id = 'ENDPOINT_ID';
    }

    public function test_it_creates_endpoint()
    {
        $userOptions = [
            'interface' => 'admin',
            'name'      => 'name',
            'region'    => 'RegionOne',
            'url'       => 'foo.com',
            'serviceId' => '12345'
        ];

        $userJson = $userOptions;
        $userJson['service_id'] = $userOptions['serviceId'];
        unset($userJson['serviceId']);

        $request = $this->setupMockRequest('POST', 'endpoints', ['endpoint' => $userJson]);
        $this->setupMockResponse($request, 'endpoint');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Endpoint */
        $endpoint = $this->service->createEndpoint($userOptions);

        $this->assertInstanceOf(Endpoint::class, $endpoint);
    }

    public function test_it_updates_endpoint()
    {
        $this->endpoint->interface = 'admin';
        $this->endpoint->name = 'name';
        $this->endpoint->region = 'RegionOne';
        $this->endpoint->url = 'foo.com';
        $this->endpoint->serviceId = '12345';

        $userJson = [
            'interface'  => 'admin',
            'name'       => 'name',
            'region'     => 'RegionOne',
            'url'        => 'foo.com',
            'service_id' => '12345'
        ];

        $request = $this->setupMockRequest('PATCH', 'endpoints/ENDPOINT_ID', ['endpoint' => $userJson]);
        $this->setupMockResponse($request, 'endpoint');

        $this->endpoint->update();
    }

    public function test_it_deletes_endpoint()
    {
        $request = $this->setupMockRequest('DELETE', 'endpoints/ENDPOINT_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->endpoint->delete();
    }
}