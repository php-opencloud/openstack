<?php

namespace OpenStack\Test\Images\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Images\v2\Api;
use OpenStack\Images\v2\Models\Member;
use OpenStack\Test\TestCase;

class MemberTest extends TestCase
{
    private $member;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->member = new Member($this->client->reveal(), new Api());
        $this->member->imageId = 'foo';
        $this->member->id = 'bar';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'v2/images/foo/members/bar', 'GET_member', null, []);

        $this->member->retrieve();
    }

    public function test_it_updates()
    {
        $expectedJson = ['status' => 'rejected'];

        $this->mockRequest('PUT', 'v2/images/foo/members/bar', 'GET_member', $expectedJson, []);

        $this->member->updateStatus(Member::STATUS_REJECTED);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'v2/images/foo/members/bar', new Response(204), null, []);

        $this->member->delete();
    }
}
