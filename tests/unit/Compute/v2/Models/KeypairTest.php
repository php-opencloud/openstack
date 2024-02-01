<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Test\TestCase;
use OpenStack\Compute\v2\Models\Keypair;

class KeypairTest extends TestCase
{
    /**@var Keypair */
    private $keypair;

    const KEYPAIR_NAME = 'keypair-test';

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->keypair = new Keypair($this->client->reveal(), new Api());
        $this->keypair->id = 1;
        $this->keypair->name = self::KEYPAIR_NAME;
    }

    public function test_it_creates()
    {
        $opts = [
            'name'        => self::KEYPAIR_NAME,
            'publicKey'   => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAgQDx8nkQv/zgGgB4rMYmIf+6A4l6Rr+o/6lHBQdW5aYd44bd8JttDCE/F/pNRr0lRE+PiqSPO8nDPHw0010JeMH9gYgnnFlyY3/OcJ02RhIPyyxYpv9FhY+2YiUkpwFOcLImyrxEsYXpD/0d3ac30bNH6Sw9JD9UZHYcpSxsIbECHw=='
        ];

        $expectedJson = \json_encode(['keypair' => [
            'name'       => $opts['name'],
            'public_key' => $opts['publicKey'],
        ]], JSON_UNESCAPED_SLASHES);

        $this->mockRequest('POST', 'os-keypairs', 'keypair-post', $expectedJson, ['Content-Type' => 'application/json']);

        self::assertInstanceOf(Keypair::class, $this->keypair->create($opts));
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'os-keypairs/' . self::KEYPAIR_NAME, 'keypair-get', null, []);

        $this->keypair->retrieve();

        self::assertEquals('1', $this->keypair->id);
        self::assertEquals('fake', $this->keypair->userId);
        self::assertEquals('44:fe:29:6e:23:14:b9:53:5b:65:82:58:1c:fe:5a:c3', $this->keypair->fingerprint);
        self::assertEquals(self::KEYPAIR_NAME, $this->keypair->name);
        self::assertEquals(
            'ssh-rsa AAAAAAABBBBBBBBBCCCCCCCCCCC foo@bar.com',
            $this->keypair->publicKey
        );
        self::assertFalse($this->keypair->deleted);
    }

    public function test_it_retrieves_by_user_id()
    {
        $this->mockRequest(
            'GET',
            ['path' => 'os-keypairs/' . self::KEYPAIR_NAME, 'query' => ['user_id' => 'fake']],
            'keypair-get'
        );

        $this->keypair->userId = 'fake';
        $this->keypair->retrieve();

        self::assertEquals('1', $this->keypair->id);
        self::assertEquals('fake', $this->keypair->userId);
        self::assertEquals('44:fe:29:6e:23:14:b9:53:5b:65:82:58:1c:fe:5a:c3', $this->keypair->fingerprint);
        self::assertEquals(self::KEYPAIR_NAME, $this->keypair->name);
        self::assertEquals(
            'ssh-rsa AAAAAAABBBBBBBBBCCCCCCCCCCC foo@bar.com',
            $this->keypair->publicKey
        );
        self::assertFalse($this->keypair->deleted);
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'os-keypairs/' . self::KEYPAIR_NAME, new Response(204), null, []);
        $this->keypair->delete();
    }
}
