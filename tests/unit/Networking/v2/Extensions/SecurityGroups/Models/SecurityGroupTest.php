<?php declare(strict_types=1);

namespace OpenStack\Test\Networking\v2\Extensions\SecurityGroups\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\Test\TestCase;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Api;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;

class SecurityGroupTest extends TestCase
{
    /** @var SecurityGroup */
    private $securityGroup;

    const SECURITY_GROUP_ID = '85cc3048-abc3-43cc-89b3-377341426ac5';

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->securityGroup = new SecurityGroup($this->client->reveal(), new Api());
        $this->securityGroup->id = self::SECURITY_GROUP_ID;
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'v2.0/security-groups/' . self::SECURITY_GROUP_ID, new Response(202), null, []);

        $this->securityGroup->delete();
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'v2.0/security-groups/' . self::SECURITY_GROUP_ID, 'SecurityGroup', null, []);

        $this->securityGroup->retrieve();

        self::assertEquals('test_security_group', $this->securityGroup->name);
        self::assertEquals('test_security_group_description', $this->securityGroup->description);
        self::assertEquals(self::SECURITY_GROUP_ID, $this->securityGroup->id);
        self::assertEquals(2, count($this->securityGroup->securityGroupRules));
    }

    public function test_it_updates()
    {
        $this->mockRequest('PUT', 'v2.0/security-groups/' . self::SECURITY_GROUP_ID, 'SecurityGroup', null, []);

        $this->securityGroup->update();
    }

    public function test_it_creates()
    {
        $opts = [
            'name'      => 'bar',
            'description' => 'foo',
        ];

        $expectedJson = [
            'security_group' => [
                'name'        => $opts['name'],
                'description' => $opts['description'],
            ],
        ];

        $this->mockRequest('POST', 'v2.0/security-groups', 'SecurityGroup', $expectedJson, []);

        self::assertInstanceOf(SecurityGroup::class, $this->securityGroup->create($opts));
    }
}
