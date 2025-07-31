<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Domain;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class DomainTest extends TestCase
{
    private $domain;

    public function setUp(): void
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->domain = new Domain($this->client->reveal(), new Api());
        $this->domain->id = 'DOMAIN_ID';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'domains/DOMAIN_ID', 'domain', null, []);

        $this->domain->retrieve();
    }

    public function test_it_updates()
    {
        $this->domain->description = 'foo';
        $this->domain->name = 'bar';
        $this->domain->enabled = false;

        $expectedJson = [
            'description' => 'foo',
            'name' => 'bar',
            'enabled' => false,
        ];

        $this->mockRequest('PATCH', 'domains/DOMAIN_ID', 'domain', ['domain' => $expectedJson], []);

        $this->domain->update();
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'domains/DOMAIN_ID', new Response(204), null, []);

        $this->domain->delete();
    }

    public function test_it_lists_user_roles()
    {
        $fn = $this->createFn($this->domain, 'listUserRoles', ['userId' => 'USER_ID']);
        $this->listTest($fn, 'domains/DOMAIN_ID/users/USER_ID/roles', 'Role', 'roles-user');
    }

    public function test_it_grants_user_role()
    {
        $this->mockRequest('PUT', 'domains/DOMAIN_ID/users/USER_ID/roles/ROLE_ID', new Response(204));
        $this->domain->grantUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_checks_user_role()
    {
        $this->mockRequest('HEAD', 'domains/DOMAIN_ID/users/USER_ID/roles/ROLE_ID', new Response(200));
        self::assertTrue($this->domain->checkUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_checks_nonexistent_user_role()
    {
        $this->mockRequest('HEAD', 'domains/DOMAIN_ID/users/USER_ID/roles/ROLE_ID', new BadResponseError());

        self::assertFalse($this->domain->checkUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_revokes_user_role()
    {
        $this->mockRequest('DELETE', 'domains/DOMAIN_ID/users/USER_ID/roles/ROLE_ID', new Response(204));
        $this->domain->revokeUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_lists_group_roles()
    {
        $fn = $this->createFn($this->domain, 'listGroupRoles', ['groupId' => 'GROUP_ID']);
        $this->listTest($fn, 'domains/DOMAIN_ID/groups/GROUP_ID/roles', 'Role', 'domain-group-roles');
    }

    public function test_it_grants_group_role()
    {
        $this->mockRequest('PUT', 'domains/DOMAIN_ID/groups/GROUP_ID/roles/ROLE_ID', new Response(204));
        $this->domain->grantGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_checks_group_role()
    {
        $this->mockRequest('HEAD', 'domains/DOMAIN_ID/groups/GROUP_ID/roles/ROLE_ID', new Response(200));
        self::assertTrue($this->domain->checkGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_checks_nonexistent_group_role()
    {
        $this->mockRequest('HEAD', 'domains/DOMAIN_ID/groups/GROUP_ID/roles/ROLE_ID', new BadResponseError());

        self::assertFalse($this->domain->checkGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_revokes_group_role()
    {
        $this->mockRequest('DELETE', 'domains/DOMAIN_ID/groups/GROUP_ID/roles/ROLE_ID', new Response(204), null, []);
        $this->domain->revokeGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']);
    }
}
