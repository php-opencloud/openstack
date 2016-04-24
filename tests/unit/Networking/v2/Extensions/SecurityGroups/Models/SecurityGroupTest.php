<?php declare (strict_types=1);

namespace OpenStack\Test\Networking\v2\Extensions\SecurityGroups\Models;

use GuzzleHttp\Psr7\Response;
use OpenCloud\Test\TestCase;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Api;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;

class SecurityGroupTest extends TestCase
{
    /** @var SecurityGroup */
    private $securityGroup;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->securityGroup = new SecurityGroup($this->client->reveal(), new Api());
        $this->securityGroup->id = 'id';
    }

    public function test_it_deletes()
    {
        $this->setupMock('DELETE', 'v2.0/security-groups/id', null, [], new Response(202));

        $this->securityGroup->delete();
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'v2.0/security-groups/id', null, [], 'SecurityGroup');

        $this->securityGroup->retrieve();
    }
    
    public function test_it_updates()
    {
        $this->setupMock('PUT', 'v2.0/security-groups/id', null, [], 'SecurityGroup');

        $this->securityGroup->update();
    }

    public function test_it_creates()
    {
        $opts = [
            'name'      => 'bar',
            'description' => 'foo',
        ];

        $expectedJson = json_encode(
            [
                'security_group' => [
                    'name'        => $opts['name'],
                    'description' => $opts['description'],
                ],
            ],
            JSON_UNESCAPED_SLASHES
        );

        $this->setupMock('POST', 'v2.0/security-groups', $expectedJson, ['Content-Type' => 'application/json'], 'SecurityGroup');

        $this->assertInstanceOf(SecurityGroup::class, $this->securityGroup->create($opts));
    }
}