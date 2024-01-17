<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Compute\v2\Models\Keypair;

class KeypairTest extends TestCase
{
    public function testCreate(): Keypair
    {
        $name = $this->randomStr();
        $publicKey = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCp4H/vDGnLi0QgWgMsQkv//FEz0xgv/mujVX+XCh6fHXxc/PbaASY+MsoI2Xr238cG9eaeAAUvbpJuEuHQ0M9WX97bvsWaWzLQ9F6hzLAwUBGxcG8cSh1nB3Ah7alR2nbIZ1N94yE72hXLb1AGogJ97NBVIph438BCXUNejqoOBsXL8UBP3RGdPnTHJ/6XSMaNTQAJruQMoQwecyGFQmuS2IEy2mBOmSldD6JZirHpj7PTCKJY4CS89QChGpKIeOymKn4tEQQVVtNFUyULEMdin88H1yMftPfq7QqH+ULFT2X2XvP3CI+sESq84lrIcVu7LjJCRIwlKsnMu2ESYCdz foo@bar.com';

        /** @var Keypair $keypair */
        require_once $this->sampleFile(
            'keypairs/create_keypair.php',
            [
                '{name}'      => $name,
                '{publicKey}' => $publicKey,
            ]
        );

        $this->assertInstanceOf(Keypair::class, $keypair);
        $this->assertEquals($name, $keypair->name);
        $this->assertEquals($publicKey, $keypair->publicKey);

        return $keypair;
    }

    /**
     * @depends testCreate
     */
    public function testList(Keypair $createdKeypair)
    {
        $found = false;
        require_once $this->sampleFile(
            'keypairs/list_keypairs.php',
            [
                '/** @var \OpenStack\Compute\v2\Models\Keypair $keypair */' => <<<'PHP'
/** @var \OpenStack\Compute\v2\Models\Keypair $keypair */
if ($keypair->id === $createdKeypair->id) {
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
    public function testGet(Keypair $createdKeypair)
    {
        /** @var \OpenStack\Compute\v2\Models\Keypair $keypair */
        require_once $this->sampleFile('keypairs/get_keypair.php', ['{name}' => $createdKeypair->name]);

        $this->assertInstanceOf(Keypair::class, $keypair);
        $this->assertEquals($createdKeypair->name, $keypair->name);
    }


    /**
     * @depends testCreate
     */
    public function testDelete(Keypair $createdKeypair)
    {
        require_once $this->sampleFile('keypairs/delete_keypair.php', ['{name}' => $createdKeypair->name]);

        foreach ($this->getService()->listKeypairs() as $keypair) {
            $this->assertNotEquals($createdKeypair->name, $keypair->name);
        }

        $this->expectException(BadResponseError::class);
        $createdKeypair->retrieve();
    }
}