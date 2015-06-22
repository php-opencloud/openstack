<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Project;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class ProjectTest extends TestCase
{
    private $project;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->project = new Project($this->client->reveal(), new Api());
        $this->project->id = 'PROJECT_ID';
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'projects/PROJECT_ID');
        $this->setupMockResponse($request, 'project');

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

        $request = $this->setupMockRequest('PATCH', 'projects/PROJECT_ID', ['project' => $expectedJson]);
        $this->setupMockResponse($request, 'project');

        $this->project->update();
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'projects/PROJECT_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->project->delete();
    }

    public function test_it_lists_user_roles()
    {
        $fn = $this->createFn($this->project, 'listUserRoles', ['userId' => 'USER_ID']);
        $this->listTest($fn, 'projects/PROJECT_ID/users/USER_ID/roles', 'Role', 'roles');
    }

    public function test_it_grants_user_role()
    {
        $request = $this->setupMockRequest('PUT', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->project->grantUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_checks_user_role()
    {
        $request = $this->setupMockRequest('HEAD', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(200));

        $this->assertTrue($this->project->checkUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_checks_nonexistent_user_role()
    {
        $request = $this->setupMockRequest('HEAD', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID');

        $this->client
            ->send(Argument::is($request))
            ->shouldBeCalled()
            ->willThrow(new BadResponseError());

        $this->assertFalse($this->project->checkUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_revokes_user_role()
    {
        $request = $this->setupMockRequest('DELETE', 'projects/PROJECT_ID/users/USER_ID/roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->project->revokeUserRole(['userId' => 'USER_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_lists_group_roles()
    {
        $fn = $this->createFn($this->project, 'listGroupRoles', ['groupId' => 'GROUP_ID']);
        $this->listTest($fn, 'projects/PROJECT_ID/groups/GROUP_ID/roles', 'Role', 'roles');
    }

    public function test_it_grants_group_role()
    {
        $request = $this->setupMockRequest('PUT', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->project->grantGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']);
    }

    public function test_it_checks_group_role()
    {
        $request = $this->setupMockRequest('HEAD', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(200));

        $this->assertTrue($this->project->checkGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_checks_nonexistent_group_role()
    {
        $request = $this->setupMockRequest('HEAD', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID');

        $this->client
            ->send(Argument::is($request))
            ->shouldBeCalled()
            ->willThrow(new BadResponseError());

        $this->assertFalse($this->project->checkGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']));
    }

    public function test_it_revokes_group_role()
    {
        $request = $this->setupMockRequest('DELETE', 'projects/PROJECT_ID/groups/GROUP_ID/roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->project->revokeGroupRole(['groupId' => 'GROUP_ID', 'roleId' => 'ROLE_ID']);
    }
}