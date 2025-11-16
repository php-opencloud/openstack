<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Models\Credential;

class CredentialTest extends TestCase
{
    public function testCreate(): Credential
    {
        /** @var $credential \OpenStack\Identity\v3\Models\Role */
        require_once $this->sampleFile('credentials/create.php', [
            '{blob}' => '{"access":"181920","secret":"secretKey"}',
            '{type}' => 'ec2',
        ]);
        $this->assertInstanceOf(Credential::class, $credential);

        return $credential;
    }

    /**
     * @depends testCreate
     */
    public function testList(Credential $createdCredential): void
    {
        $found = false;
        require_once $this->sampleFile(
            'credentials/list.php',
            [
                '/** @var $credential \OpenStack\Identity\v3\Models\Credential */' => <<<'PHP'
/** @var $credential \OpenStack\Identity\v3\Models\Credential */
if ($credential->id === $createdCredential->id) {
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
     */
    public function testRead(Credential $createdCredential)
    {
        /** @var $credential \OpenStack\Identity\v3\Models\Credential */
        require_once $this->sampleFile(
            'credentials/read.php',
            ['{credentialId}' => $createdCredential->id]
        );

        $this->assertInstanceOf(Credential::class, $credential);
        $this->assertEquals($createdCredential->blob, $credential->blob);
        $this->assertEquals($createdCredential->type, $credential->type);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Credential $createdCredential)
    {
        $newBlob = '{"access":"181920","secret":"newSecretKey"}';

        /** @var $credential \OpenStack\Identity\v3\Models\Credential */
        require_once $this->sampleFile(
            'credentials/update.php',
            [
                '{credentialId}' => $createdCredential->id,
                '{blob}' => $newBlob,
                '{type}' => 'ec3',
            ]
        );

        $this->assertInstanceOf(Credential::class, $credential);
        $this->assertEquals($newBlob, $credential->blob);
        $this->assertEquals('ec3', $credential->type);
    }


    /**
     * @depends testCreate
     */
    public function testDelete(Credential $createdCredential): void
    {
        require_once $this->sampleFile(
            'credentials/delete.php',
            [
                '{credentialId}' => $createdCredential->id,
            ]
        );

        $found = false;
        foreach ($this->getService()->listCredentials() as $credential) {
            if ($credential->id === $createdCredential->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdCredential->retrieve();
    }
}