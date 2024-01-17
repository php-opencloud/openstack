<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Identity\v3\Models;
use OpenStack\Identity\v3\Models\ApplicationCredential;

class ApplicationCredentialTest extends TestCase
{

    public function testAdd(): ApplicationCredential
    {
        $name = $this->randomStr();
        $description = $this->randomStr();

        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            'application_credentials/add_application_credential.php',
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
     * @depends testAdd
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
     * @depends testAdd
     */
    public function testRetrieve(ApplicationCredential $originalApplicationCredential)
    {
        /** @var $applicationCredential \OpenStack\Identity\v3\Models\ApplicationCredential */
        require_once $this->sampleFile(
            'application_credentials/show_application_credential.php',
            ['{applicationCredentialId}' => $originalApplicationCredential->id]
        );

        $this->assertInstanceOf(Models\ApplicationCredential::class, $applicationCredential);
        $this->assertEquals($originalApplicationCredential->name, $applicationCredential->name);
        $this->assertEquals($originalApplicationCredential->description, $applicationCredential->description);
    }

    /**
     * @depends testAdd
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
            'application_credentials/delete_application_credential.php',
            [
                '{applicationCredentialId}' => $applicationCredential->id,
            ]
        );

        $this->assertFalse($token->validate());
    }

}