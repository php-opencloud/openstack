<?php

namespace OpenStack\Test\BlockStorage\v2\Models;

use GuzzleHttp\Psr7\Response;
use OpenStack\BlockStorage\v2\Api;
use OpenStack\BlockStorage\v2\Models\QuotaSet;
use OpenStack\Test\TestCase;

class QuotaSetTest extends TestCase
{
    /** @var QuotaSet */
    protected $quotaSet;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->quotaSet = new QuotaSet($this->client->reveal(), new Api());
        $this->quotaSet->tenantId = 'tenant-foo';
    }

    public function test_it_retrieves()
    {
        $this->mockRequest('GET', 'os-quota-sets/tenant-foo', 'GET_quota_set', [], []);

        $this->quotaSet->retrieve();
        self::assertEquals(1, $this->quotaSet->gigabytes);
        self::assertEquals(2, $this->quotaSet->snapshots);
        self::assertEquals(3, $this->quotaSet->volumes);
    }

    public function test_it_updates()
    {
        $expectedJson = [
            'quota_set' => [
                'snapshots' => 2222,
                'volumes'   => 1111,
            ],
        ];

        $this->mockRequest('PUT', 'os-quota-sets/tenant-foo', 'GET_type', $expectedJson, []);

        $this->quotaSet->volumes = 1111;
        $this->quotaSet->snapshots = 2222;
        $this->quotaSet->update();
    }

    public function test_it_deletes()
    {
        $this->mockRequest('DELETE', 'os-quota-sets/tenant-foo', new Response(204), null, []);

        $this->quotaSet->delete();
    }
}
