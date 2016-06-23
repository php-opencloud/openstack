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

    public function setUp()
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

        $this->setupMock('POST', 'os-keypairs', $expectedJson, ['Content-Type' => 'application/json'], 'keypair-post');

        $this->assertInstanceOf(Keypair::class, $this->keypair->create($opts));
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'os-keypairs/' . self::KEYPAIR_NAME, null, [], 'keypair-get');

        $this->keypair->retrieve();

        $this->assertEquals('1', $this->keypair->id);
        $this->assertEquals('fake', $this->keypair->userId);
        $this->assertEquals('44:fe:29:6e:23:14:b9:53:5b:65:82:58:1c:fe:5a:c3', $this->keypair->fingerprint);
        $this->assertEquals(self::KEYPAIR_NAME, $this->keypair->name);
        $this->assertEquals(
            'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC1HTrHCbb9NawNLSV8N6tSa8i637+EC2dA+lsdHHfQlT54t+N0nHhJPlKWDLhc579j87vp6RDFriFJ/smsTnDnf64O12z0kBaJpJPH2zXrBkZFK6q2rmxydURzX/z0yLSCP77SFJ0fdXWH2hMsAusflGyryHGX20n+mZK6mDrxVzGxEz228dwQ5G7Az5OoZDWygH2pqPvKjkifRw0jwUKf3BbkP0QvANACOk26cv16mNFpFJfI1N3OC5lUsZQtKGR01ptJoWijYKccqhkAKuo902tg/qup58J5kflNm7I61sy1mJon6SGqNUSfoQagqtBH6vd/tU1jnlwZ03uUroAL',
            $this->keypair->publicKey
        );
        $this->assertFalse($this->keypair->deleted);
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'os-keypairs/' . self::KEYPAIR_NAME, null, [], new Response(204));
        $this->keypair->delete();
    }
}
