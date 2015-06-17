<?php

namespace OpenStack\Test\Identity\v3;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Group;
use OpenStack\Test\TestCase;

class GroupTest extends TestCase
{
    private $group;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);
        parent::setUp();

        $this->group = new Group($this->client->reveal(), new Api());
        $this->group->id = 'GROUP_ID';
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

        $request = $this->setupMockRequest('POST', 'groups', ['group' => $userJson]);
        $this->setupMockResponse($request, 'group');

        /** @var $group \OpenStack\Identity\v3\Models\Group */
        $group = $this->group->create($userOptions);

        $this->assertInstanceOf(Group::class, $group);
    }

    public function test_it_updates_group()
    {
        $this->group->description = 'desc';
        $this->group->name = 'name';

        $userJson = ['description'  => 'desc', 'name' => 'name'];

        $request = $this->setupMockRequest('PATCH', 'groups/GROUP_ID', ['group' => $userJson]);
        $this->setupMockResponse($request, 'endpoint');

        $this->group->update();
    }

    public function test_it_deletes_group()
    {
        $request = $this->setupMockRequest('DELETE', 'groups/GROUP_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->group->delete();
    }

    public function test_it_lists_users()
    {
        $fn = $this->createFn($this->group, 'listUsers', []);
        $this->listTest($fn, 'groups/GROUP_ID/users', 'User', 'users');
    }

    public function test_it_adds_users()
    {
        $request = $this->setupMockRequest('PUT', 'groups/GROUP_ID/users/USER_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->group->addUser(['userId' => 'USER_ID']);
    }

    public function test_it_removes_users()
    {
        $request = $this->setupMockRequest('DELETE', 'groups/GROUP_ID/users/USER_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->group->removeUser(['userId' => 'USER_ID']);
    }

    public function test_it_checks_user_memberships()
    {
        $request = $this->setupMockRequest('HEAD', 'groups/GROUP_ID/users/USER_ID');
        $this->setupMockResponse($request, new Response(200));

        $this->group->checkMembership(['userId' => 'USER_ID']);
    }
}