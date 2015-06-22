<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Role;
use OpenStack\Test\TestCase;

class RoleTest extends TestCase
{
    private $role;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->role = new Role($this->client->reveal(), new Api());
        $this->role->id = 'ROLE_ID';
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'roles/ROLE_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->role->delete();
    }
} 