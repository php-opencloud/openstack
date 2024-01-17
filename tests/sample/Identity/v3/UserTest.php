<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Models\User;

class UserTest extends TestCase
{
    public function testAdd(): User
    {
        $domain = $this->getService()->createDomain(['name' => $this->randomStr()]);
        $project = $this->getService()->createProject(['name' => $this->randomStr(), 'domainId' => $domain->id]);

        /** @var $user \OpenStack\Identity\v3\Models\User */
        require_once $this->sampleFile(
            'users/add_user.php',
            [
                '{defaultProjectId}' => $project->id,
                '{description}'      => $this->randomStr(),
                '{domainId}'         => $domain->id,
                '{email}'            => 'foo@bar.com',
                '{enabled}'          => true,
                '{name}'             => $this->randomStr(),
                '{userPass}'         => $this->randomStr(),
            ]
        );
        $this->assertInstanceOf(User::class, $user);

        return $user;
    }

    /**
     * @depends testAdd
     */
    public function testGet(User $createdUser): void
    {
        /** @var $user \OpenStack\Identity\v3\Models\User */
        require_once $this->sampleFile('users/get_user.php', ['{id}' => $createdUser->id]);
        $this->assertInstanceOf(User::class, $user);

        $this->assertEquals($createdUser->id, $user->id);
        $this->assertEquals($createdUser->name, $user->name);
        $this->assertEquals($createdUser->description, $user->description);
    }

    /**
     * @depends testAdd
     */
    public function testList(User $createdUser): void
    {
        $found = false;
        require_once $this->sampleFile(
            'users/list_users.php',
            [
                '/** @var $user \OpenStack\Identity\v3\Models\User */' => <<<'PHP'
/** @var $user \OpenStack\Identity\v3\Models\User */
if ($user->id === $createdUser->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testAdd
     */
    public function testListGroups(User $createdUser): void
    {
        $createdGroup = $this->getService()->createGroup(['name' => $this->randomStr(), 'domainId' => $createdUser->domainId]);
        $createdGroup->addUser(['userId' => $createdUser->id]);

        $found = false;
        require_once $this->sampleFile(
            'users/list_groups.php',
            [
                '{id}' => $createdUser->id,
                '/** @var $group \OpenStack\Identity\v3\Models\Group */' => <<<'PHP'
/** @var $group \OpenStack\Identity\v3\Models\Group */
if ($group->id === $createdGroup->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testAdd
     */
    public function testListProjects(User $createdUser): void
    {
        $createdRole = $this->getService()->createRole(['name' => $this->randomStr()]);

        $createdProject = $this->getService()->createProject(['name' => $this->randomStr(), 'domainId' => $createdUser->domainId]);
        $createdProject->grantUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]);

        $found = false;
        require_once $this->sampleFile(
            'users/list_projects.php',
            [
                '{id}' => $createdUser->id,
                '/** @var $project \OpenStack\Identity\v3\Models\Project */' => <<<'PHP'
/** @var $project \OpenStack\Identity\v3\Models\Project */
if ($project->id === $createdProject->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testAdd
     */
    public function testUpdate(User $createdUser): void
    {
        $newName = $this->randomStr();
        $newDescription = $this->randomStr();

        require_once $this->sampleFile(
            'users/update_user.php',
            [
                '{id}'          => $createdUser->id,
                '{name}'        => $newName,
                '{description}' => $newDescription,
            ]
        );

        $createdUser->retrieve();

        $this->assertEquals($newName, $createdUser->name);
        $this->assertEquals($newDescription, $createdUser->description);
    }

    /**
     * @depends testAdd
     */
    public function testDelete(User $createdUser): void
    {
        require_once $this->sampleFile('users/delete_user.php', ['{id}' => $createdUser->id]);

        $found = false;
        foreach ($this->getService()->listUsers() as $user) {
            if ($user->id === $createdUser->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdUser->retrieve();
    }
}