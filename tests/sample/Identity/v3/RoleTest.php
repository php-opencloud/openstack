<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Identity\v3\Models\Role;

class RoleTest extends TestCase
{
    public function testCeate(): Role
    {
        /** @var $role \OpenStack\Identity\v3\Models\Role */
        require_once $this->sampleFile('roles/create.php', ['{name}' => $this->randomStr()]);
        $this->assertInstanceOf(Role::class, $role);

        return $role;
    }

    /**
     * @depends testCeate
     */
    public function testList(Role $createdRole): void
    {
        $found = false;
        require_once $this->sampleFile(
            'roles/list.php',
            [
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
     * @depends testCeate
     */
    public function testListAssignments(Role $createdRole): void
    {
        $createdDomain = $this->getService()->createDomain(['name' => $this->randomStr()]);
        $createdUser = $this->getService()->createUser(['name' => $this->randomStr(), 'domainId' => $createdDomain->id]);

        $createdDomain->grantUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]);

        $found = false;
        require_once $this->sampleFile(
            'roles/list_assignments.php',
            [
                '/** @var $assignment \OpenStack\Identity\v3\Models\Assignment */' => <<<'PHP'
/** @var $assignment \OpenStack\Identity\v3\Models\Assignment */
if ($assignment->user !== null && $assignment->user->id === $createdUser->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }
}