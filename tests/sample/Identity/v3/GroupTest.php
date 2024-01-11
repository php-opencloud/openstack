<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Models\Group;

class GroupTest extends TestCase
{
    public function testAdd(): array
    {
        $name = $this->randomStr();
        $description = $this->randomStr();

        /** @var $group \OpenStack\Identity\v3\Models\Group */
        require_once $this->sampleFile(
            'groups/add_group.php',
            [
                '{name}'        => $name,
                '{description}' => $description,
            ]
        );

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($name, $group->name);
        $this->assertEquals($description, $group->description);

        $user = $this->getService()->createUser(['name' => $this->randomStr()]);

        return [$group, $user];
    }

    /**
     * @depends testAdd
     */
    public function testAddUser(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        /** @var $createdUser \OpenStack\Identity\v3\Models\User */
        [$createdGroup, $createdUser] = $groupAndUser;

        $this->assertFalse($createdGroup->checkMembership(['userId' => $createdUser->id]));

        require_once $this->sampleFile(
            'groups/add_user.php',
            [
                '{groupId}'     => $createdGroup->id,
                '{groupUserId}' => $createdUser->id,
            ]
        );

        $this->assertTrue($createdGroup->checkMembership(['userId' => $createdUser->id]));
    }

    /**
     * @depends testAdd
     */
    public function testCheckMembership(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        /** @var $createdUser \OpenStack\Identity\v3\Models\User */
        [$createdGroup, $createdUser] = $groupAndUser;

        /** @var $result bool */
        require_once $this->sampleFile(
            'groups/check_user_membership.php',
            [
                '{groupId}'     => $createdGroup->id,
                '{groupUserId}' => $createdUser->id,
            ]
        );

        $this->assertTrue($result);
    }

    /**
     * @depends testAdd
     */
    public function testListUsers(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        /** @var $createdUser \OpenStack\Identity\v3\Models\User */
        [$createdGroup, $createdUser] = $groupAndUser;

        $found = false;
        require_once $this->sampleFile(
            'groups/list_users.php',
            [
                '{groupId}'                                            => $createdGroup->id,
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
    public function testRemoveUser(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        /** @var $createdUser \OpenStack\Identity\v3\Models\User */
        [$createdGroup, $createdUser] = $groupAndUser;

        $this->assertTrue($createdGroup->checkMembership(['userId' => $createdUser->id]));

        require_once $this->sampleFile(
            'groups/remove_user.php',
            [
                '{groupId}'     => $createdGroup->id,
                '{groupUserId}' => $createdUser->id,
            ]
        );

        $this->assertFalse($createdGroup->checkMembership(['userId' => $createdUser->id]));
    }

    /**
     * @depends testAdd
     */
    public function testGet(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        [$createdGroup] = $groupAndUser;

        /** @var $group \OpenStack\Identity\v3\Models\Group */
        require_once $this->sampleFile(
            'groups/get_group.php',
            [
                '{groupId}' => $createdGroup->id,
            ]
        );

        $this->assertEquals($createdGroup->id, $group->id);
        $this->assertEquals($createdGroup->name, $group->name);
        $this->assertEquals($createdGroup->description, $group->description);
    }

    /**
     * @depends testAdd
     */
    public function testUpdate(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        [$createdGroup] = $groupAndUser;

        $newName = $this->randomStr();
        $newDescription = $this->randomStr();

        require_once $this->sampleFile(
            'groups/update_group.php',
            [
                '{groupId}'     => $createdGroup->id,
                '{name}'        => $newName,
                '{description}' => $newDescription,
            ]
        );

        $createdGroup->retrieve();
        $this->assertEquals($newName, $createdGroup->name);
        $this->assertEquals($newDescription, $createdGroup->description);
    }

    /**
     * @depends testAdd
     */
    public function testList(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        [$createdGroup] = $groupAndUser;

        $found = false;
        require_once $this->sampleFile(
            'groups/list_groups.php',
            [
                '{groupId}'                                            => $createdGroup->id,
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
    public function testDelete(array $groupAndUser): void
    {
        /** @var $createdGroup \OpenStack\Identity\v3\Models\Group */
        [$createdGroup] = $groupAndUser;

        require_once $this->sampleFile(
            'groups/delete_group.php',
            [
                '{groupId}' => $createdGroup->id,
            ]
        );

        $found = false;
        foreach ($this->getService()->listGroups() as $group) {
            if ($group->id === $createdGroup->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdGroup->retrieve();
    }
}