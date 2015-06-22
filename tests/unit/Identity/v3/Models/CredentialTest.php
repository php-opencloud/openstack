<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\Credential;
use OpenStack\Test\TestCase;

class CredentialTest extends TestCase
{
    private $credential;

    public function setUp()
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->credential = new Credential($this->client->reveal(), new Api());
        $this->credential->id = 'CRED_ID';
    }

    public function test_it_retrieves()
    {
        $request = $this->setupMockRequest('GET', 'credentials/CRED_ID');
        $this->setupMockResponse($request, 'cred');

        $this->credential->retrieve();
    }

    public function test_it_updates()
    {
        $this->credential->type = 'foo';
        $this->credential->projectId = 'bar';

        $expectedJson = [
            'type' => 'foo',
            'project_id' => 'bar',
        ];

        $request = $this->setupMockRequest('PATCH', 'credentials/CRED_ID', $expectedJson);
        $this->setupMockResponse($request, 'cred');

        $this->credential->update();
    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'credentials/CRED_ID');
        $this->setupMockResponse($request, new Response(204));

        $this->credential->delete();
    }
}