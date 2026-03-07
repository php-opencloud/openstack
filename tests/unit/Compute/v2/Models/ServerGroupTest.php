<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\ServerGroup;
use OpenStack\Test\TestCase;

class ServerGroupTest extends TestCase
{
    private const SERVER_GROUP_ID = '4f62479e-cf47-4d14-bc41-0fc0c9db79f3';

    /** @var ServerGroup */
    private $serverGroup;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->serverGroup = new ServerGroup($this->client->reveal(), new Api());
        $this->serverGroup->id = self::SERVER_GROUP_ID;
    }

    public function test_it_retrieves_details()
    {
        $this->mockRequest('GET', 'os-server-groups/' . self::SERVER_GROUP_ID, 'server-group-get', null, []);

        $this->serverGroup->retrieve();

        self::assertEquals(self::SERVER_GROUP_ID, $this->serverGroup->id);
        self::assertEquals('db-group', $this->serverGroup->name);
        self::assertEquals('anti-affinity', $this->serverGroup->policy);
        self::assertEquals(['anti-affinity'], $this->serverGroup->policies);
        self::assertEquals('project-123', $this->serverGroup->projectId);
        self::assertEquals('user-456', $this->serverGroup->userId);
        self::assertEquals(['server-1', 'server-2'], $this->serverGroup->members);
        self::assertEquals(1, $this->serverGroup->rules['max_server_per_host']);
        self::assertSame([], $this->serverGroup->metadata);
    }

    public function test_it_creates()
    {
        $opts = [
            'name'     => 'web-group',
            'policies' => ['affinity'],
        ];

        $expectedJson = ['server_group' => [
            'name'     => $opts['name'],
            'policies' => $opts['policies'],
        ]];

        $this->mockRequest('POST', 'os-server-groups', 'server-group-post', $expectedJson, []);

        self::assertInstanceOf(ServerGroup::class, $this->serverGroup->create($opts));
        self::assertEquals('affinity', $this->serverGroup->policy);
        self::assertEquals(['affinity'], $this->serverGroup->policies);
        self::assertSame([], $this->serverGroup->rules);
    }

    public function test_it_creates_with_policy_and_rules()
    {
        $opts = [
            'name'   => 'db-group',
            'policy' => 'anti-affinity',
            'rules'  => ['max_server_per_host' => 1],
        ];

        $expectedJson = ['server_group' => [
            'name'   => $opts['name'],
            'policy' => $opts['policy'],
            'rules'  => $opts['rules'],
        ]];

        $this->mockRequest('POST', 'os-server-groups', 'server-group-get', $expectedJson, []);

        self::assertInstanceOf(ServerGroup::class, $this->serverGroup->create($opts));
        self::assertEquals('anti-affinity', $this->serverGroup->policy);
        self::assertEquals(['anti-affinity'], $this->serverGroup->policies);
        self::assertEquals(1, $this->serverGroup->rules['max_server_per_host']);
    }

    public function test_it_requires_a_policy_definition()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('"policy" or "policies" must be set.');

        $this->serverGroup->create(['name' => 'missing-policy']);
    }

    public function test_it_requires_policy_when_rules_are_provided()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('"rules" requires "policy".');

        $this->serverGroup->create([
            'name'  => 'missing-policy',
            'rules' => ['max_server_per_host' => 1],
        ]);
    }

    public function test_it_rejects_policy_and_policies_together()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Provide either "policy" or "policies", not both.');

        $this->serverGroup->create([
            'name'     => 'mixed-group',
            'policy'   => 'anti-affinity',
            'policies' => ['affinity'],
        ]);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'os-server-groups/' . self::SERVER_GROUP_ID, new Response(204), null, []);

        $this->serverGroup->delete();
    }
}
