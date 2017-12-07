<?php

namespace OpenStack\Test\Identity\v3;

use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Enum;
use OpenStack\Identity\v3\Models;
use OpenStack\Identity\v3\Service;
use OpenStack\Test\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;

class ServiceTest extends TestCase
{
    /** @var Service */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_authenticates()
    {
        $userOptions = [
            'user'        => [
                'id'       => '{userId}',
                'password' => '{userPassword}',
                'domain'   => ['id' => '{domainId}']
            ],
            'scope'       => [
                'project' => ['id' => '{projectId}']
            ],
            'catalogName' => 'swift',
            'catalogType' => 'object-store',
            'region'      => 'RegionOne',
        ];

        $expectedJson = [
            "identity" => [
                "methods"  => ["password"],
                "password" => [
                    "user" => [
                        "id"       => "{userId}",
                        "password" => "{userPassword}",
                        'domain'   => ['id' => '{domainId}']
                    ]
                ]
            ],
            "scope"    => [
                "project" => ["id" => "{projectId}"]
            ]
        ];

        $this->setupMock('POST', 'auth/tokens', ['auth' => $expectedJson], [], 'token');

        list($token, $url) = $this->service->authenticate($userOptions);

        $this->assertInstanceOf(Models\Token::class, $token);
        $this->assertEquals('http://example.org:8080/v1/AUTH_e00abf65afca49609eedd163c515cf10', $url);
    }

    public function test_it_authenticates_using_cache_token()
    {
        $cachedToken = [
            'is_domain'  => false,
            'methods'    => [
                'password',
            ],
            'roles'      => [
                0 => [
                    'id'   => 'ce40dfb7a1b14f8a875194fe2944e00c',
                    'name' => 'admin',
                ],
            ],
            'expires_at' => '2199-11-24T04:47:49.000000Z',
            'project'    => [
                'domain' => [
                    'id'   => 'default',
                    'name' => 'Default',
                ],
                'id'     => 'c41b19de8aac4ecdb0f04ede718206c5',
                'name'   => 'admin',
            ],
            'catalog' => [
                [
                    'endpoints' => [
                        [
                            'region_id' => 'RegionOne',
                            'url'       => 'http://example.org:8080/v1/AUTH_e00abf65afca49609eedd163c515cf10',
                            'region'    => 'RegionOne',
                            'interface' => 'public',
                            'id'        => 'hhh',
                        ]
                    ],
                    'type'      => 'object-store',
                    'id'        => 'aaa',
                    'name'      => 'swift',
                ],
            ],
            'user'       => [
                'domain' => [
                    'id'   => 'default',
                    'name' => 'Default',
                ],
                'id'     => '37a36374b074428985165e80c9ab28c8',
                'name'   => 'admin',
            ],
            'audit_ids'  => [
                'X0oY7ouSQ32vEpbgDJTDpA',
            ],
            'issued_at'  => '2017-11-24T03:47:49.000000Z',
            'id'         => 'bb4f74cfb73847ec9ca947fa61d799d3',
        ];

        $userOptions = [
            'user'        => [
                'id'       => '{userId}',
                'password' => '{userPassword}',
                'domain'   => ['id' => '{domainId}']
            ],
            'scope'       => [
                'project' => ['id' => '{projectId}']
            ],
            'catalogName' => 'swift',
            'catalogType' => 'object-store',
            'region'      => 'RegionOne',
            'cachedToken' => $cachedToken
        ];

        list($token, $url) = $this->service->authenticate($userOptions);

        $this->assertInstanceOf(Models\Token::class, $token);
        $this->assertEquals('http://example.org:8080/v1/AUTH_e00abf65afca49609eedd163c515cf10', $url);
    }


    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cached token has expired
     */
    public function test_it_authenticates_and_throws_exception_when_authenticate_with_expired_cached_token()
    {
        $cachedToken = [
            'is_domain'  => false,
            'methods'    => [
                'password',
            ],
            'roles'      => [
                0 => [
                    'id'   => 'ce40dfb7a1b14f8a875194fe2944e00c',
                    'name' => 'admin',
                ],
            ],
            'expires_at' => '2000-11-24T04:47:49.000000Z',
            'project'    => [
                'domain' => [
                    'id'   => 'default',
                    'name' => 'Default',
                ],
                'id'     => 'c41b19de8aac4ecdb0f04ede718206c5',
                'name'   => 'admin',
            ],
            'catalog' => [
                [
                    'endpoints' => [
                        [
                            'region_id' => 'RegionOne',
                            'url'       => 'http://example.org:8080/v1/AUTH_e00abf65afca49609eedd163c515cf10',
                            'region'    => 'RegionOne',
                            'interface' => 'public',
                            'id'        => 'hhh',
                        ]
                    ],
                    'type'      => 'object-store',
                    'id'        => 'aaa',
                    'name'      => 'swift',
                ],
            ],
            'user'       => [
                'domain' => [
                    'id'   => 'default',
                    'name' => 'Default',
                ],
                'id'     => '37a36374b074428985165e80c9ab28c8',
                'name'   => 'admin',
            ],
            'audit_ids'  => [
                'X0oY7ouSQ32vEpbgDJTDpA',
            ],
            'issued_at'  => '2017-11-24T03:47:49.000000Z',
            'id'         => 'bb4f74cfb73847ec9ca947fa61d799d3',
        ];

        $userOptions = [
            'user'        => [
                'id'       => '{userId}',
                'password' => '{userPassword}',
                'domain'   => ['id' => '{domainId}']
            ],
            'scope'       => [
                'project' => ['id' => '{projectId}']
            ],
            'catalogName' => 'swift',
            'catalogType' => 'object-store',
            'region'      => 'RegionOne',
            'cachedToken' => $cachedToken
        ];

        $this->service->authenticate($userOptions);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_it_throws_exception_if_no_endpoint_found()
    {
        $expectedJson = [
            "identity" => [
                "methods"  => ["password"],
                "password" => [
                    "user" => [
                        "id"       => "{userId}",
                        "password" => "{userPassword}",
                        'domain'   => ['id' => '{domainId}']
                    ]
                ]
            ],
            "scope"    => [
                "project" => ["id" => "{projectId}"]
            ]
        ];

        $this->setupMock('POST', 'auth/tokens', ['auth' => $expectedJson], [], 'token');

        $this->service->authenticate([
            'catalogName' => 'foo',
            'catalogType' => 'bar',
            'region'      => 'baz',
            'user'        => [
                'id'       => '{userId}',
                'password' => '{userPassword}',
                'domain'   => ['id' => '{domainId}']
            ],
            'scope'       => [
                'project' => ['id' => '{projectId}']
            ],
        ]);
    }

    public function test_it_gets_token()
    {
        $this->setupMock('GET', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId'], 'token-get');

        $token = $this->service->getToken('tokenId');
        $token->retrieve();

        $this->assertInstanceOf(Models\Token::class, $token);
        $this->assertEquals(new \DateTimeImmutable('2013-02-27T18:30:59.999999Z'), $token->expires);
        $this->assertEquals(new \DateTimeImmutable('2013-02-27T16:30:59.999999Z'), $token->issued);
        $this->assertEquals(['password'], $token->methods);

        $user = $this->service->model(Models\User::class, [
            "domain" => [
                "id"    => "1789d1",
                "links" => [
                    "self" => "http://identity:35357/v3/domains/1789d1"
                ],
                "name"  => "example.com"
            ],
            "id"     => "0ca8f6",
            "links"  => [
                "self" => "http://identity:35357/v3/users/0ca8f6"
            ],
            "name"   => "Joe"
        ]);
        $this->assertEquals($user, $token->user);
    }

    public function test_false_is_returned_when_token_validation_returns_204()
    {
        $this->setupMock('HEAD', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId'], new Response(204));

        $this->assertTrue($this->service->validateToken('tokenId'));
    }

    public function test_true_is_returned_when_token_validation_returns_error()
    {
        $this->client
            ->request('HEAD', 'auth/tokens', ['headers' => ['X-Subject-Token' => 'tokenId']])
            ->shouldBeCalled()
            ->willThrow(new BadResponseError());

        $this->assertFalse($this->service->validateToken('tokenId'));
    }

    public function test_it_revokes_token()
    {
        $this->setupMock('DELETE', 'auth/tokens', [], ['X-Subject-Token' => 'tokenId'], new Response(204));

        $this->assertNull($this->service->revokeToken('tokenId'));
    }

    public function test_it_creates_service()
    {
        $userOptions = ['name' => 'foo', 'type' => 'bar', 'description' => 'description'];

        $this->setupMock('POST', 'services', ['service' => $userOptions], [], 'service');

        $service = $this->service->createService($userOptions);

        $this->assertInstanceOf(Models\Service::class, $service);
        $this->assertEquals('serviceId', $service->id);
        $this->assertEquals('foo', $service->name);
        $this->assertEquals('bar', $service->type);
    }

    public function test_it_lists_services()
    {
        $this->listTest($this->createFn($this->service, 'listServices', []), 'services', 'Service');
    }

    public function test_it_gets_service()
    {
        $this->getTest($this->createFn($this->service, 'getService', 'id'), 'service');
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
        $expectedJson = json_encode($expectedJson, JSON_UNESCAPED_SLASHES);

        $this->setupMock('POST', 'endpoints', $expectedJson, ['Content-Type' => 'application/json'], 'endpoint');

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
            'enabled'     => true,
            'name'        => 'foo'
        ];

        $this->setupMock('POST', 'domains', ['domain' => $userOptions], [], 'domain');

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
        $this->listTest($this->createFn($this->service, 'listDomains', []), 'domains', 'Domain');
    }

    public function test_it_gets_domain()
    {
        $this->getTest($this->createFn($this->service, 'getDomain', 'id'), 'domain');
    }

    public function test_it_creates_project()
    {
        $userOptions = [
            'description' => 'bar',
            'enabled'     => true,
            'name'        => 'foo'
        ];

        $this->setupMock('POST', 'projects', ['project' => $userOptions], [], 'project');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Project */
        $project = $this->service->createProject($userOptions);

        $this->assertInstanceOf(Models\Project::class, $project);

        $this->assertEquals('456789', $project->id);
        $this->assertTrue($project->enabled);
        $this->assertEquals('myNewProject', $project->name);
    }

    public function test_it_lists_projects()
    {
        $this->setupMock('GET', 'projects', null, [], 'projects');

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
        $this->getTest($this->createFn($this->service, 'getProject', 'id'), 'project');
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

        $this->setupMock('POST', 'users', ['user' => $userJson], [], 'user');

        /** @var $endpoint \OpenStack\Identity\v3\Models\User */
        $user = $this->service->createUser($userOptions);

        $this->assertInstanceOf(Models\User::class, $user);

        $this->assertEquals('263fd9', $user->defaultProjectId);
        $this->assertEquals("Jim Doe's user", $user->description);
        $this->assertEquals("1789d1", $user->domainId);
        $this->assertEquals("jdoe@example.com", $user->email);
        $this->assertTrue($user->enabled);
        $this->assertEquals('ff4e51', $user->id);
        $this->assertEquals('jdoe', $user->name);
    }

    public function test_it_lists_users()
    {
        $this->listTest($this->createFn($this->service, 'listUsers', []), 'users', 'User');
    }

    public function test_it_gets_user()
    {
        $this->getTest($this->createFn($this->service, 'getUser', 'id'), 'user');
    }

    public function test_it_creates_group()
    {
        $userOptions = [
            'description' => "description",
            'name'        => 'name',
        ];

        $this->setupMock('POST', 'groups', ['group' => $userOptions], [], 'group');

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
        $this->listTest($this->createFn($this->service, 'listGroups', []), 'groups', 'Group');
    }

    public function test_it_gets_group()
    {
        $this->getTest($this->createFn($this->service, 'getGroup', 'id'), 'group');
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

        $this->setupMock('POST', 'credentials', $userJson, [], 'cred');

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
        $this->listTest($this->createFn($this->service, 'listCredentials', []), 'credentials', 'Credential');
    }

    public function test_it_gets_credential()
    {
        $this->getTest($this->createFn($this->service, 'getCredential', 'id'), 'credential');
    }

    public function test_it_creates_role()
    {
        $userOptions = ['name' => 'a role name'];

        $this->setupMock('POST', 'roles', ['role' => $userOptions], [], 'role');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Role */
        $role = $this->service->createRole($userOptions);

        $this->assertInstanceOf(Models\Role::class, $role);

        $this->assertEquals($userOptions['name'], $role->name);
    }

    public function test_it_lists_roles()
    {
        $this->listTest($this->createFn($this->service, 'listRoles', []), 'roles', 'Role');
    }

    public function test_it_lists_role_assignments()
    {
        $fn = $this->createFn($this->service, 'listRoleAssignments', []);
        $this->listTest($fn, 'role_assignments', 'Assignment');
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

        $this->setupMock('POST', 'policies', ['policy' => $userJson], [], 'policy');

        /** @var $endpoint \OpenStack\Identity\v3\Models\Policy */
        $policy = $this->service->createPolicy($userOptions);

        $this->assertInstanceOf(Models\Policy::class, $policy);
    }

    public function test_it_lists_policies()
    {
        $this->listTest($this->createFn($this->service, 'listPolicies', []), 'policies', 'Policy');
    }

    public function test_it_gets_policy()
    {
        $this->getTest($this->createFn($this->service, 'getPolicy', 'id'), 'policy');
    }

    public function test_it_generates_tokens_with_user_creds()
    {
        $userOptions = [
            'user'  => [
                'id'       => '{userId}',
                'password' => '{userPassword}',
                'domain'   => ['id' => '{domainId}']
            ],
            'scope' => [
                'project' => ['id' => '{projectId}']
            ]
        ];

        $expectedJson = [
            "identity" => [
                "methods"  => ["password"],
                "password" => [
                    "user" => [
                        "id"       => "{userId}",
                        "password" => "{userPassword}",
                        'domain'   => ['id' => '{domainId}']
                    ]
                ]
            ],
            "scope"    => [
                "project" => ["id" => "{projectId}"]
            ]
        ];

        $this->setupMock('POST', 'auth/tokens', ['auth' => $expectedJson], [], 'token');

        $token = $this->service->generateToken($userOptions);
        $this->assertInstanceOf(Models\Token::class, $token);
    }

    public function test_it_generates_token_with_token_id()
    {
        $userOptions = [
            'tokenId' => '{tokenId}',
            'scope'   => [
                'project' => ['id' => '{projectId}']
            ]
        ];

        $expectedJson = [
            "identity" => [
                "token"   => ['id' => '{tokenId}'],
                'methods' => ['token'],
            ],
            "scope"    => [
                "project" => ["id" => "{projectId}"]
            ]
        ];

        $this->setupMock('POST', 'auth/tokens', ['auth' => $expectedJson], [], 'token');

        $token = $this->service->generateToken($userOptions);
        $this->assertInstanceOf(Models\Token::class, $token);
    }

    public function test_it_generates_token_from_cache()
    {
        $cache = [
            'id' => 'some-token-id'
        ];

        $this->client
            ->request('POST', 'auth/tokens', Argument::any())
            ->shouldNotBeCalled()
            ->willReturn($this->getFixture('token-get'));

        $token = $this->service->generateTokenFromCache($cache);

        $this->assertInstanceOf(Models\Token::class, $token);
        $this->assertEquals('some-token-id', $token->id);
    }

    public function test_it_lists_endpoints()
    {
        $this->listTest($this->createFn($this->service, 'listEndpoints', []), 'endpoints', 'Endpoint');
    }

    public function test_it_gets_endpoint()
    {
        $this->getTest($this->createFn($this->service, 'getEndpoint', 'id'), 'endpoint');
    }
}
