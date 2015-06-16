<?php

namespace OpenStack\test_it_\Identity\v3;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Enum;
use OpenStack\Identity\v3\Models;
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

    private function listTest($resourceName)
    {
        $request = $this->setupMockRequest('GET', $resourceName . 's');
        $this->setupMockResponse($request, $resourceName . 's');

        $resources = $this->service->{'list' . ucfirst($resourceName) . 's'}();

        $this->assertInstanceOf('\Generator', $resources);

        $count = 0;

        foreach ($resources as $resource) {
            $this->assertInstanceOf('OpenStack\Identity\v3\Models\\' . ucfirst($resourceName), $resource);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    private function getTest($resourceName)
    {
        $resource = $this->service->{'get' . ucfirst($resourceName)}('id');

        $this->assertInstanceOf('OpenStack\Identity\v3\Models\\' . ucfirst($resourceName), $resource);
        $this->assertEquals('id', $resource->id);
    }

    public function test_it_gets_token()
    {
        $request = $this->setupMockRequest('GET', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId']);
        $this->setupMockResponse($request, 'token-get');

        $token = $this->service->getToken('tokenId');
        $token->retrieve();

        $this->assertInstanceOf(Models\Token::class, $token);
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

        $this->assertInstanceOf(Models\Service::class, $service);
        $this->assertEquals('serviceId', $service->id);
        $this->assertEquals('foo', $service->name);
        $this->assertEquals('bar', $service->type);
    }

    public function test_it_lists_services()
    {
        $this->listTest('service');
    }

    public function test_it_gets_service()
    {
        $this->getTest('service');
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

        $this->assertInstanceOf(Models\Endpoint::class, $endpoint);

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

        $this->assertInstanceOf(Models\Domain::class, $domain);

        $this->assertEquals('12345', $domain->id);
        $this->assertTrue($domain->enabled);
        $this->assertEquals('foo', $domain->name);
        $this->assertEquals('bar', $domain->description);
    }

    public function test_it_lists_domains()
    {
        $this->listTest('domain');
    }

    public function test_it_gets_domain()
    {
        $this->getTest('domain');
    }

    public function test_it_creates_project()
    {
        $userOptions = [
            'description' => 'bar',
            'enabled'     => true,
            'name'        => 'foo'
        ];

        $request = $this->setupMockRequest('POST', 'projects', ['project' => $userOptions]);
        $this->setupMockResponse($request, 'project');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Project */
        $project = $this->service->createProject($userOptions);

        $this->assertInstanceOf(Models\Project::class, $project);

        $this->assertEquals('456789', $project->id);
        $this->assertTrue($project->enabled);
        $this->assertEquals('myNewProject', $project->name);
    }

    public function test_it_lists_projects()
    {
        $request = $this->setupMockRequest('GET', 'projects');
        $this->setupMockResponse($request, 'projects');

        $projects = $this->service->listProjects();

        $this->assertInstanceOf('\Generator', $projects);

        $count = 0;

        foreach ($projects as $project) {
            $this->assertInstanceOf(Models\Project::class, $project);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    public function test_it_gets_project()
    {
        $this->getTest('project');
    }

    public function test_it_creates_user()
    {
        $userOptions = [
            'defaultProjectId' => 'bar',
            'description'      => "Jim Doe's user",
            'domainId'         => 'foo',
            'email'            => 'baz',
            'enabled'          => true,
            'name'             => 'James Doe',
            'password'         => 'secret'
        ];

        $userJson = $userOptions;
        $userJson['default_project_id'] = $userJson['defaultProjectId'];
        $userJson['domain_id'] = $userJson['domainId'];
        unset($userJson['defaultProjectId'], $userJson['domainId']);

        $request = $this->setupMockRequest('POST', 'users', ['user' => $userJson]);
        $this->setupMockResponse($request, 'user');

        /** @var $endpoint \OpenStack\Identity\v3\Models\User */
        $user = $this->service->createUser($userOptions);

        $this->assertInstanceOf(Models\User::class, $user);

        $this->assertEquals('263fd9', $user->defaultProjectId);
        $this->assertEquals("Jim Doe's user", $user->description);
        $this->assertEquals("1789d1", $user->domain->id);
        $this->assertEquals("jdoe@example.com", $user->email);
        $this->assertTrue($user->enabled);
        $this->assertEquals('ff4e51', $user->id);
        $this->assertEquals('jdoe', $user->name);
    }

    public function test_it_lists_users()
    {
        $this->listTest('user');
    }

    public function test_it_gets_user()
    {
        $this->getTest('user');
    }

    public function test_it_creates_group()
    {
        $userOptions = [
            'description' => "description",
            'name'        => 'name',
        ];

        $request = $this->setupMockRequest('POST', 'groups', ['group' => $userOptions]);
        $this->setupMockResponse($request, 'group');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Group */
        $group = $this->service->createGroup($userOptions);

        $this->assertInstanceOf(Models\Group::class, $group);

        $this->assertEquals($userOptions['description'], $group->description);
        $this->assertEquals($userOptions['name'], $group->name);
        $this->assertEquals('id', $group->id);
        $this->assertEquals('domain_id', $group->domainId);
    }

    public function test_it_lists_groups()
    {
        $this->listTest('group');
    }

    public function test_it_gets_group()
    {
        $this->getTest('group');
    }

    public function test_it_creates_credential()
    {
        $userOptions = [
            'blob'      => "{\"access\":\"--access-key--\",\"secret\":\"--secret-key--\"}",
            'projectId' => 'project_id',
            'type'      => 'ec2',
            'userId'    => 'user_id'
        ];

        $userJson = [
            'blob'       => $userOptions['blob'],
            'project_id' => $userOptions['projectId'],
            'type'       => $userOptions['type'],
            'user_id'    => $userOptions['userId'],
        ];

        $request = $this->setupMockRequest('POST', 'credentials', $userJson);
        $this->setupMockResponse($request, 'cred');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Credential */
        $cred = $this->service->createCredential($userOptions);

        $this->assertInstanceOf(Models\Credential::class, $cred);

        $this->assertEquals($userOptions['blob'], $cred->blob);
        $this->assertEquals($userOptions['projectId'], $cred->projectId);
        $this->assertEquals('id', $cred->id);
        $this->assertEquals($userOptions['type'], $cred->type);
    }

    public function test_it_lists_credentials()
    {
        $this->listTest('credential');
    }

    public function test_it_gets_credential()
    {
        $this->getTest('credential');
    }

    public function test_it_creates_role()
    {
        $userOptions = ['name' => 'a role name'];

        $request = $this->setupMockRequest('POST', 'roles', ['role' => $userOptions]);
        $this->setupMockResponse($request, 'role');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Role */
        $role = $this->service->createRole($userOptions);

        $this->assertInstanceOf(Models\Role::class, $role);

        $this->assertEquals($userOptions['name'], $role->name);
    }

    public function test_it_lists_roles()
    {
        $this->listTest('role');
    }

    public function test_it_lists_role_assignments()
    {
        $request = $this->setupMockRequest('GET', 'role_assignments');
        $this->setupMockResponse($request, 'role-assignments');

        $resources = $this->service->listRoleAssignments();

        $this->assertInstanceOf('\Generator', $resources);

        $count = 0;

        foreach ($resources as $resource) {
            $this->assertInstanceOf(Models\Assignment::class, $resource);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    public function test_it_creates_policy()
    {
        $userOptions = [
            'blob'      => 'blob',
            'projectId' => 'project_id',
            'type'      => 'ec2',
            'userId'    => 'user_id'
        ];

        $userJson = [
            'blob'       => $userOptions['blob'],
            'project_id' => $userOptions['projectId'],
            'type'       => $userOptions['type'],
            'user_id'    => $userOptions['userId'],
        ];

        $request = $this->setupMockRequest('POST', 'policies', $userJson);
        $this->setupMockResponse($request, 'policy');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Policy */
        $policy = $this->service->createPolicy($userOptions);

        $this->assertInstanceOf(Models\Policy::class, $policy);
    }

    public function test_it_lists_policies()
    {
        $request = $this->setupMockRequest('GET', 'policies');
        $this->setupMockResponse($request, 'policies');

        $resources = $this->service->listPolicies();

        $this->assertInstanceOf('\Generator', $resources);

        $count = 0;

        foreach ($resources as $resource) {
            $this->assertInstanceOf(Models\Policy::class, $resource);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    public function test_it_gets_policy()
    {
        $this->getTest('policy');
    }
}