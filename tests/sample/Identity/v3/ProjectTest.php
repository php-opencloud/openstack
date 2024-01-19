<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Models\Project;

class ProjectTest extends TestCase
{
    public function testCreate(): Project
    {
        /** @var $project \OpenStack\Identity\v3\Models\Project */
        require_once $this->sampleFile(
            'projects/create.php',
            ['{name}' => $this->randomStr(), '{description}' => $this->randomStr()]
        );
        $this->assertInstanceOf(Project::class, $project);

        return $project;
    }

    /**
     * @depends testCreate
     */
    public function testRead(Project $createdProject)
    {
        /** @var $project \OpenStack\Identity\v3\Models\Project */
        require_once $this->sampleFile('projects/read.php', ['{id}' => $createdProject->id]);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($createdProject->id, $project->id);
        $this->assertEquals($createdProject->name, $project->name);
    }

    /**
     * @depends testCreate
     */
    public function testGrantGroupRole(Project $createdProject): array
    {
        $domain = $this->getService()->createDomain(['name' => $this->randomStr()]);
        $role = $this->getService()->createRole(['name' => $this->randomStr()]);
        $group = $this->getService()->createGroup(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        require_once $this->sampleFile(
            'projects/grant_group_role.php',
            [
                '{id}'      => $createdProject->id,
                '{groupId}' => $group->id,
                '{roleId}'  => $role->id,
            ]
        );

        $this->assertTrue($createdProject->checkGroupRole(['groupId' => $group->id, 'roleId' => $role->id]));

        return [$role, $group];
    }

    /**
     * @depends testCreate
     * @depends testGrantGroupRole
     */
    public function testCheckGroupRole(Project $createdProject, array $createdRoleAndGroup)
    {
        [$createdRole, $createdGroup] = $createdRoleAndGroup;

        /** @var $result bool */
        require_once $this->sampleFile(
            'projects/check_group_role.php',
            [
                '{id}'      => $createdProject->id,
                '{groupId}' => $createdGroup->id,
                '{roleId}'  => $createdRole->id,
            ]
        );

        $this->assertTrue($result);
    }

    /**
     * @depends testCreate
     * @depends testGrantGroupRole
     */
    public function testListGroupRoles(Project $createdProject, array $createdRoleAndGroup)
    {
        [$createdRole, $createdGroup] = $createdRoleAndGroup;

        $found = false;
        require_once $this->sampleFile(
            'projects/list_group_roles.php',
            [
                '{id}'                                                 => $createdProject->id,
                '{groupId}'                                            => $createdGroup->id,
                '/** @var $role \OpenStack\Identity\v3\Models\Role */' => <<<'PHP'
/** @var $role \OpenStack\Identity\v3\Models\Role */
if ($role->id === $createdRole->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     * @depends testGrantGroupRole
     */
    public function testRevokeGroupRole(Project $createdProject, array $createdRoleAndGroup)
    {
        [$createdRole, $createdGroup] = $createdRoleAndGroup;

        $this->assertTrue($createdProject->checkGroupRole(['groupId' => $createdGroup->id, 'roleId' => $createdRole->id]));

        require_once $this->sampleFile(
            'projects/revoke_group_role.php',
            [
                '{id}'      => $createdProject->id,
                '{groupId}' => $createdGroup->id,
                '{roleId}'  => $createdRole->id,
            ]
        );

        $this->assertFalse($createdProject->checkGroupRole(['groupId' => $createdGroup->id, 'roleId' => $createdRole->id]));
    }

    /**
     * @depends testCreate
     */
    public function testGrantUserRole(Project $createdProject): array
    {
        $domain = $this->getService()->createDomain(['name' => $this->randomStr()]);
        $role = $this->getService()->createRole(['name' => $this->randomStr()]);
        $user = $this->getService()->createUser(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        require_once $this->sampleFile(
            'projects/grant_user_role.php',
            [
                '{id}'            => $createdProject->id,
                '{projectUserId}' => $user->id,
                '{roleId}'        => $role->id,
            ]
        );

        $this->assertTrue($createdProject->checkUserRole(['userId' => $user->id, 'roleId' => $role->id]));
        return [$role, $user];
    }

    /**
     * @depends testCreate
     * @depends testGrantUserRole
     */
    public function testCheckUserRole(Project $createdProject, array $createdRoleAndUser)
    {
        [$createdRole, $createdUser] = $createdRoleAndUser;

        /** @var $result bool */
        require_once $this->sampleFile(
            'projects/check_user_role.php',
            [
                '{id}'            => $createdProject->id,
                '{projectUserId}' => $createdUser->id,
                '{roleId}'        => $createdRole->id,
            ]
        );

        $this->assertTrue($result);
    }

    /**
     * @depends testCreate
     * @depends testGrantUserRole
     */
    public function testListUserRoles(Project $createdProject, array $createdRoleAndUser)
    {
        [$createdRole, $createdUser] = $createdRoleAndUser;

        $found = false;
        require_once $this->sampleFile(
            'projects/list_user_roles.php',
            [
                '{id}'                                                 => $createdProject->id,
                '{projectUserId}'                                      => $createdUser->id,
                '/** @var $role \OpenStack\Identity\v3\Models\Role */' => <<<'PHP'
/** @var $role \OpenStack\Identity\v3\Models\Role */
if ($role->id === $createdRole->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     * @depends testGrantUserRole
     */
    public function testRevokeUserRole(Project $createdProject, array $createdRoleAndUser)
    {
        [$createdRole, $createdUser] = $createdRoleAndUser;

        $this->assertTrue($createdProject->checkUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]));

        require_once $this->sampleFile(
            'projects/revoke_user_role.php',
            [
                '{id}'            => $createdProject->id,
                '{projectUserId}' => $createdUser->id,
                '{roleId}'        => $createdRole->id,
            ]
        );

        $this->assertFalse($createdProject->checkUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Project $createdProject)
    {
        $this->assertTrue($createdProject->enabled);

        require_once $this->sampleFile('projects/update.php', ['{id}' => $createdProject->id]);

        $createdProject->retrieve();
        $this->assertFalse($createdProject->enabled);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Project $createdProject)
    {
        require_once $this->sampleFile('projects/delete.php', ['{id}' => $createdProject->id]);

        $found = false;
        foreach ($this->getService()->listProjects() as $project) {
            if ($project->id === $createdProject->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdProject->retrieve();
    }
}