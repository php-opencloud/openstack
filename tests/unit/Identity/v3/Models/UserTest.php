<?php

namespace OpenStack\Test\Identity\v3;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\User;
use OpenStack\Test\TestCase;

class UserTest extends TestCase
{
    private $user;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->user = new User($this->client->reveal(), new Api());
        $this->user->id = 'USER_ID';
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'users/USER_ID');
        $this->setupMockResponse($request, 'user');

        $this->user->retrieve();
    }

    public function test_it_updates()
    {
        $this->user->defaultProjectId = 'foo';
        $this->user->description = 'desc';
        $this->user->email = 'foo@bar.com';
        $this->user->enabled = true;

        $expectedJson = [
            'default_project_id' => 'foo',
            'description' => 'desc',
            'email' => 'foo@bar.com',
            'enabled' => true,
        ];

        $request = $this->setupMockRequest('PATCH', 'users/USER_ID', ['user' => $expectedJson]);
        $this->setupMockResponse($request, 'user');

        $this->user->update();
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'users/USER_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->user->delete();
    }

    public function test_it_lists_groups()
    {
        $fn = $this->createFn($this->user, 'listGroups', []);
        $this->listTest($fn, 'users/USER_ID/groups', 'Group', 'groups');
    }

    public function test_it_lists_projects()
    {
        $fn = $this->createFn($this->user, 'listProjects', []);
        $this->listTest($fn, 'users/USER_ID/projects', 'Project', 'projects');
    }
}