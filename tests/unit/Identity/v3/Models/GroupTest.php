<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Group;
use OpenStack\Test\TestCase;
use Prophecy\Argument;

class GroupTest extends TestCase
{
    private $group;

    public function setUp(): void
    {
        $this->rootFixturesDir = dirname(__DIR__);
        parent::setUp();

        $this->group = new Group($this->client->reveal(), new Api());
        $this->group->id = 'GROUP_ID';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'groups/GROUP_ID', 'group', null, []);

        $this->group->retrieve();
    }

    public function test_it_creates_group()
    {
        $userOptions = [
            'description' => 'desc',
            'domainId'    => 'domainId',
            'name'        => 'name'
        ];

        $userJson = [
            'description' => $userOptions['description'],
            'domain_id'   => $userOptions['domainId'],
            'name'        => $userOptions['name']
        ];

        $this->mockRequest('POST', 'groups', 'group', ['group' => $userJson], []);

        /** @var $group \OpenStack\Identity\v3\Models\Group */
        $group = $this->group->create($userOptions);

        self::assertInstanceOf(Group::class, $group);
    }

    public function test_it_updates_group()
    {
        $this->group->description = 'desc';
        $this->group->name = 'name';

        $userJson = ['description'  => 'desc', 'name' => 'name'];

        $this->mockRequest('PATCH', 'groups/GROUP_ID', 'endpoint', ['group' => $userJson], []);

        $this->group->update();
    }

    public function test_it_deletes_group()
    {
        $this->mockRequest('DELETE', 'groups/GROUP_ID', new Response(204), null, []);

        $this->group->delete();
    }

    public function test_it_lists_users()
    {
        $fn = $this->createFn($this->group, 'listUsers', []);
        $this->listTest($fn, 'groups/GROUP_ID/users', 'User', 'users');
    }

    public function test_it_adds_users()
    {
        $this->mockRequest('PUT', 'groups/GROUP_ID/users/USER_ID', new Response(204), null, []);

        $this->group->addUser(['userId' => 'USER_ID']);
    }

    public function test_it_removes_users()
    {
        $this->mockRequest('DELETE', 'groups/GROUP_ID/users/USER_ID', new Response(204), null, []);

        $this->group->removeUser(['userId' => 'USER_ID']);
    }

    public function test_it_checks_user_memberships()
    {
        $this->mockRequest('HEAD', 'groups/GROUP_ID/users/USER_ID', new Response(200), null, []);

        $this->group->checkMembership(['userId' => 'USER_ID']);
    }

    public function test_it_checks_nonexistent_memberships()
    {
        $this->mockRequest('HEAD', 'groups/GROUP_ID/users/USER_ID', new BadResponseError());

        self::assertFalse($this->group->checkMembership(['userId' => 'USER_ID']));
    }
}
