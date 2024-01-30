<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Identity\v3\Models;
use OpenStack\Identity\v3\Models\ApplicationCredential;

class ApplicationCredentialTest extends TestCase
{

    public function testCreate(): ApplicationCredential
    {
        $name = $this->randomStr();
        $description = $this->randomStr();

        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            'application_credentials/create.php',
            [
                '{name}'        => $name,
                '{description}' => $description,
            ]
        );

        $this->assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        $this->assertEquals($name, $applicationCredential->name);
        $this->assertEquals($description, $applicationCredential->description);

        return $applicationCredential;
    }

    /**
     * @depends testCreate
     */
    public function testGenerateToken(ApplicationCredential $applicationCredential)
    {
        /** @var $token \OpenStack\Identity\v3\Models\Token */
        require_once $this->sampleFile(
            'tokens/generate_token_with_application_credential_id.php',
            [
                '{applicationCredentialId}' => $applicationCredential->id,
                '{secret}'                  => $applicationCredential->secret,
            ]
        );

        $this->assertInstanceOf(Models\Token::class, $token);
        $this->assertTrue($token->validate());
    }

    /**
     * @depends testCreate
     */
    public function testRead(ApplicationCredential $originalApplicationCredential)
    {
        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            'application_credentials/read.php',
            ['{applicationCredentialId}' => $originalApplicationCredential->id]
        );

        $this->assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        $this->assertEquals($originalApplicationCredential->name, $applicationCredential->name);
        $this->assertEquals($originalApplicationCredential->description, $applicationCredential->description);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(ApplicationCredential $applicationCredential)
    {
        $token = $this->getService()->generateToken([
            'application_credential' => [
                'id'     => $applicationCredential->id,
                'secret' => $applicationCredential->secret,
            ]
        ]);

        $this->assertTrue($token->validate());

        require_once $this->sampleFile(
            'application_credentials/delete.php',
            [
                '{applicationCredentialId}' => $applicationCredential->id,
            ]
        );

        $this->assertFalse($token->validate());
    }

}