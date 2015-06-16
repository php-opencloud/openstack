<?php

namespace OpenStack\test_it_\Identity\v3;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Enum;
use OpenStack\Identity\v3\Models\Domain;
use OpenStack\Identity\v3\Models\Endpoint;
use OpenStack\Identity\v3\Models\Token;
use OpenStack\Identity\v3\Models\User;
use OpenStack\Identity\v3\Models\Service as ServiceModel;
use OpenStack\Identity\v3\Service;
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

    public function test_it_gets_token()
    {
        $request = $this->setupMockRequest('GET', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId']);
        $this->setupMockResponse($request, 'token-get');

        $token = $this->service->getToken('tokenId');

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals(new \DateTimeImmutable('2013-02-27T18:30:59.999999Z'), $token->expires);
        $this->assertEquals(new \DateTimeImmutable('2013-02-27T16:30:59.999999Z'), $token->issued);
        $this->assertEquals(['password'], $token->methods);

        $user = $this->service->model('User', [
            "domain" => [
                "id" => "1789d1",
                "links" => [
                    "self" => "http://identity:35357/v3/domains/1789d1"
                ],
                "name" => "example.com"
            ],
            "id" => "0ca8f6",
            "links" => [
                "self" => "http://identity:35357/v3/users/0ca8f6"
            ],
            "name" => "Joe"
        ]);
        $this->assertEquals($user, $token->user);
    }

    public function test_false_is_returned_when_token_validation_returns_204()
    {
        $request = $this->setupMockRequest('HEAD', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId']);
        $this->setupMockResponse($request, new Response(204));

        $this->assertTrue($this->service->validateToken('tokenId'));
    }

    public function test_true_is_returned_when_token_validation_returns_error()
    {
        $request = $this->setupMockRequest('HEAD', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId']);
        $this->setupMockResponse($request, new Response(404));

        $this->assertFalse($this->service->validateToken('tokenId'));
    }

    public function test_it_revokes_token()
    {
        $request = $this->setupMockRequest('DELETE', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId']);
        $this->setupMockResponse($request, new Response(204));

        $this->assertNull($this->service->revokeToken('tokenId'));
    }

    public function test_it_creates_service()
    {
        $userOptions = ['name' => 'foo', 'type' => 'bar'];

        $request = $this->setupMockRequest('POST', 'services', ['service' => $userOptions]);
        $this->setupMockResponse($request, 'service');

        $service = $this->service->createService($userOptions);

        $this->assertInstanceOf(ServiceModel::class, $service);
        $this->assertEquals('serviceId', $service->id);
        $this->assertEquals('foo', $service->name);
        $this->assertEquals('bar', $service->type);
    }

    public function test_it_lists_services()
    {
        $request = $this->setupMockRequest('GET', 'services');
        $this->setupMockResponse($request, 'services-get');

        $services = $this->service->listServices();

        $this->assertInstanceOf('\Generator', $services);

        $count = 0;

        foreach ($services as $service) {
            $this->assertInstanceOf(ServiceModel::class, $service);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    public function test_it_gets_service()
    {
        $service = $this->service->getService('serviceId');

        $this->assertInstanceOf(ServiceModel::class, $service);
        $this->assertEquals('serviceId', $service->id);
    }

    public function test_it_creates_endpoint()
    {
        $userOptions = [
            'interface' => Enum::INTERFACE_INTERNAL,
            'name'      => 'endpointName',
            'region'    => 'RegionOne',
            'url'       => 'myopenstack.org:12345/v2.0',
            'serviceId' => 'serviceId'
        ];

        $expectedJson = ['endpoint' => $userOptions];
        unset($expectedJson['endpoint']['serviceId']);
        $expectedJson['endpoint']['service_id'] = $userOptions['serviceId'];

        $request = $this->setupMockRequest('POST', 'endpoints', $expectedJson);
        $this->setupMockResponse($request, 'endpoint');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Endpoint */
        $endpoint = $this->service->createEndpoint($userOptions);

        $this->assertInstanceOf(Endpoint::class, $endpoint);

        $this->assertEquals($userOptions['interface'], $endpoint->interface);
        $this->assertEquals($userOptions['name'], $endpoint->name);
        $this->assertEquals($userOptions['region'], $endpoint->region);
        $this->assertEquals($userOptions['url'], $endpoint->url);
        $this->assertEquals($userOptions['serviceId'], $endpoint->serviceId);
    }

    public function test_it_creates_domain()
    {
        $userOptions = [
            'description' => 'bar',
            'enabled' => true,
            'name' => 'foo'
        ];

        $request = $this->setupMockRequest('POST', 'domains', ['domain' => $userOptions]);
        $this->setupMockResponse($request, 'domain');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Domain */
        $domain = $this->service->createDomain($userOptions);

        $this->assertInstanceOf(Domain::class, $domain);

        $this->assertEquals('12345', $domain->id);
        $this->assertTrue($domain->enabled);
        $this->assertEquals('foo', $domain->name);
        $this->assertEquals('bar', $domain->description);
    }

    public function test_it_lists_domains()
    {

    }

    public function test_it_gets_domain()
    {

    }

    public function test_it_creates_project()
    {

    }

    public function test_it_lists_projects()
    {

    }

    public function test_it_gets_project()
    {

    }

    public function test_it_creates_user()
    {

    }

    public function test_it_lists_users()
    {

    }

    public function test_it_gets_user()
    {

    }

    public function test_it_creates_group()
    {

    }

    public function test_it_lists_groups()
    {

    }

    public function test_it_gets_group()
    {

    }

    public function test_it_creates_credential()
    {

    }

    public function test_it_lists_credentials()
    {

    }

    public function test_it_gets_credential()
    {

    }

    public function test_it_creates_role()
    {

    }

    public function test_it_lists_roles()
    {

    }

    public function test_it_lists_role_assignments()
    {

    }

    public function test_it_creates_policy()
    {

    }

    public function test_it_lists_policies()
    {

    }

    public function test_it_gets_policy()
    {

    }
}