<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Project;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class ProjectTest extends TestCase
{
    private $project;

    public function setUp(): void
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->project = new Project($this->client->reveal(), new Api());
        $this->project->id = 'PROJECT_ID';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'projects/PROJECT_ID', 'project', null, []);

        $this->project->retrieve();
    }

    public function test_it_updates()
    {
        $this->project->description = 'desc';
        $this->project->domainId = 'domainId';
        $this->project->parentId = 'parentId';
        $this->project->enabled = true;
        $this->project->name = 'name';

        $expectedJson = [
            'description' => 'desc',
            'domain_id' => 'domainId',
            'parent_id' => 'parentId',
            'enabled' => true,
            'name' => 'name',
        ];

        $this->mockRequest('PATCH', 'projects/PROJECT_ID', 'project', ['project' => $expectedJson], []);

        $this->project->update();
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'projects/PROJECT_ID', new Response(204), null, []);

        $this->project->delete();
    }

    public function test_it_lists_user_roles()
    {
        $fn = $this->createFn($this->project, 'listUserRoles', ['userId' => 'USER_ID']);
        $this->listTest($fn, 'projects/PROJECT_ID/users/USER_ID/roles', 'Role', 'roles');
    }

    public function test_it_grants_user_role()
    {
        $this->mockRequest('PUT', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID', new Response(204), null, []);

        $this->project->grantUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_checks_user_role()
    {
        $this->mockRequest('HEAD', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID', new Response(200), null, []);

        self::assertTrue($this->project->checkUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_checks_nonexistent_user_role()
    {
        $this->mockRequest('HEAD', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID', new BadResponseError());

        self::assertFalse($this->project->checkUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_revokes_user_role()
    {
        $this->mockRequest('DELETE', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID', new Response(204), null, []);

        $this->project->revokeUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_lists_group_roles()
    {
        $fn = $this->createFn($this->project, 'listGroupRoles', ['groupId' => 'GROUP_ID']);
        $this->listTest($fn, 'projects/PROJECT_ID/groups/GROUP_ID/roles', 'Role', 'roles');
    }

    public function test_it_grants_group_role()
    {
        $this->mockRequest('PUT', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID', new Response(204), null, []);

        $this->project->grantGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_checks_group_role()
    {
        $this->mockRequest('HEAD', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID', new Response(200), null, []);

        self::assertTrue($this->project->checkGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_checks_nonexistent_group_role()
    {
        $this->mockRequest('HEAD', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID', new BadResponseError());

        self::assertFalse($this->project->checkGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_revokes_group_role()
    {
        $this->mockRequest('DELETE', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID', new Response(204), null, []);

        $this->project->revokeGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']);
    }
}
