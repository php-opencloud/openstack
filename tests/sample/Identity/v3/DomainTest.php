<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Models\Domain;

class DomainTest extends TestCase
{
    public function testCreate(): Domain
    {
        $name = $this->randomStr();
        $description = $this->randomStr();

        /** @var $domain \OpenStack\Identity\v3\Models\Domain */
        require_once $this->sampleFile(
            'domains/create.php',
            [
                '{name}'        => $name,
                '{description}' => $description,
            ]
        );
        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertEquals($name, $domain->name);
        $this->assertEquals($description, $domain->description);

        return $domain;
    }

    /**
     * @depends testCreate
     */
    public function testList(Domain $createdDomain)
    {
        $found = false;
        require_once $this->sampleFile(
            'domains/list.php',
            [
                '/** @var $domain \OpenStack\Identity\v3\Models\Domain */' => <<<'PHP'
/** @var $domain \OpenStack\Identity\v3\Models\Domain */
if ($domain->id === $createdDomain->id) {
    $found = true;
}
PHP,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     */
    public function testRead(Domain $createdDomain)
    {
        /** @var $domain \OpenStack\Identity\v3\Models\Domain */
        require_once $this->sampleFile('domains/read.php', ['{domainId}' => $createdDomain->id]);
        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertEquals($createdDomain->id, $domain->id);
        $this->assertEquals($createdDomain->name, $domain->name);
        $this->assertEquals($createdDomain->description, $domain->description);
    }


    /**
     * @depends testCreate
     */
    public function testGrantGroupRole(Domain $createdDomain): array
    {
        $createdRole = $this->getService()->createRole(['name' => $this->randomStr()]);
        $createdGroup = $this->getService()->createGroup(['name' => $this->randomStr(), 'domainId' => $createdDomain->id]);

        $this->assertFalse($createdDomain->checkGroupRole(['groupId' => $createdGroup->id, 'roleId' => $createdRole->id]));

        require_once $this->sampleFile(
            'domains/grant_group_role.php',
            [
                '{domainId}' => $createdDomain->id,
                '{roleId}'   => $createdRole->id,
                '{groupId}'  => $createdGroup->id,
            ]
        );

        $this->assertTrue($createdDomain->checkGroupRole(['groupId' => $createdGroup->id, 'roleId' => $createdRole->id]));

        return [$createdRole, $createdGroup];
    }

    /**
     * @depends testCreate
     * @depends testGrantGroupRole
     */
    public function testCheckGroupRole(Domain $createdDomain, array $createdRoleAndGroup)
    {
        [$createdRole, $createdGroup] = $createdRoleAndGroup;

        /** @var $result bool */
        require_once $this->sampleFile(
            'domains/check_group_role.php',
            [
                '{domainId}' => $createdDomain->id,
                '{roleId}'   => $createdRole->id,
                '{groupId}'  => $createdGroup->id,
            ]
        );
        self::assertTrue($result);
    }

    /**
     * @depends testCreate
     * @depends testGrantGroupRole
     */
    public function testListGroupRole(Domain $createdDomain, array $createdRoleAndGroup)
    {
        [$createdRole, $createdGroup] = $createdRoleAndGroup;

        $found = false;
        require_once $this->sampleFile(
            'domains/list_group_roles.php',
            [
                '{domainId}'                                           => $createdDomain->id,
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
    public function testRevokeGroupRole(Domain $createdDomain, array $createdRoleAndGroup)
    {
        [$createdRole, $createdGroup] = $createdRoleAndGroup;

        $this->assertTrue($createdDomain->checkGroupRole(['groupId' => $createdGroup->id, 'roleId' => $createdRole->id]));

        require_once $this->sampleFile(
            'domains/revoke_group_role.php',
            [
                '{domainId}' => $createdDomain->id,
                '{roleId}'   => $createdRole->id,
                '{groupId}'  => $createdGroup->id,
            ]
        );

        $this->assertFalse($createdDomain->checkGroupRole(['groupId' => $createdGroup->id, 'roleId' => $createdRole->id]));
    }

    /**
     * @depends testCreate
     */
    public function testGrantUserRole(Domain $createdDomain): array
    {
        $createdRole = $this->getService()->createRole(['name' => $this->randomStr()]);
        $createdUser = $this->getService()->createUser(['name' => $this->randomStr(), 'domainId' => $createdDomain->id]);

        $this->assertFalse($createdDomain->checkUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]));

        require_once $this->sampleFile(
            'domains/grant_user_role.php',
            [
                '{domainId}'     => $createdDomain->id,
                '{roleId}'       => $createdRole->id,
                '{domainUserId}' => $createdUser->id,
            ]
        );

        $this->assertTrue($createdDomain->checkUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]));
        return [$createdRole, $createdUser];
    }

    /**
     * @depends testCreate
     * @depends testGrantUserRole
     */
    public function testCheckUserRole(Domain $createdDomain, array $createdRoleAndUser)
    {
        [$createdRole, $createdUser] = $createdRoleAndUser;

        /** @var $result bool */
        require_once $this->sampleFile(
            'domains/check_user_role.php',
            [
                '{domainId}'     => $createdDomain->id,
                '{roleId}'       => $createdRole->id,
                '{domainUserId}' => $createdUser->id,
            ]
        );
        self::assertTrue($result);
    }

    /**
     * @depends testCreate
     * @depends testGrantUserRole
     */
    public function testListUserRole(Domain $createdDomain, array $createdRoleAndUser)
    {
        [$createdRole, $createdUser] = $createdRoleAndUser;

        $found = false;
        require_once $this->sampleFile(
            'domains/list_user_roles.php',
            [
                '{domainId}'                                           => $createdDomain->id,
                '{domainUserId}'                                       => $createdUser->id,
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
    public function testRevokeUserRole(Domain $createdDomain, array $createdRoleAndUser)
    {
        [$createdRole, $createdUser] = $createdRoleAndUser;

        $this->assertTrue($createdDomain->checkUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]));

        require_once $this->sampleFile(
            'domains/revoke_user_role.php',
            [
                '{domainId}'     => $createdDomain->id,
                '{roleId}'       => $createdRole->id,
                '{domainUserId}' => $createdUser->id,
            ]
        );

        $this->assertFalse($createdDomain->checkUserRole(['userId' => $createdUser->id, 'roleId' => $createdRole->id]));
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Domain $createdDomain)
    {
        $this->assertTrue($createdDomain->enabled);
        require_once $this->sampleFile('domains/update.php', ['{domainId}' => $createdDomain->id]);
        $createdDomain->retrieve();
        $this->assertFalse($createdDomain->enabled);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Domain $createdDomain)
    {
        require_once $this->sampleFile('domains/delete.php', ['{domainId}' => $createdDomain->id]);
        $found = false;
        foreach ($this->getService()->listDomains() as $domain) {
            if ($domain->id === $createdDomain->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdDomain->retrieve();
    }
}