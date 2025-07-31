<?php

namespace OpenStack\Test\Identity\v3\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Identity\v3\Api;
use OpenStack\Identity\v3\Models\ApplicationCredential;
use OpenStack\Test\TestCase;

class ApplicationCredentialTest extends TestCase
{
    private $applicationCredential;

    public function setUp(): void
    {
        $this->rootFixturesDir = dirname(__DIR__);

        parent::setUp();

        $this->applicationCredential = new ApplicationCredential($this->client->reveal(), new Api());
        $this->applicationCredential->id = 'APPLICATION_CREDENTIAL_ID';
        $this->applicationCredential->userId = 'USER_ID';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'users/USER_ID/application_credentials/APPLICATION_CREDENTIAL_ID', 'application_credential', null, []);

        $this->applicationCredential->retrieve();

        $this->assertEquals('monitoring', $this->applicationCredential->name);
        $this->assertEquals(null, $this->applicationCredential->secret);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'users/USER_ID/application_credentials/APPLICATION_CREDENTIAL_ID', new Response(204), null, []);

        $this->applicationCredential->delete();
    }
}