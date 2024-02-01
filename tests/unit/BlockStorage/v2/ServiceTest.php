<?php

namespace OpenStack\Test\BlockStorage\v2;

use GuzzleHttp\Psr7\Response;
use OpenStack\BlockStorage\v2\Api;
use OpenStack\BlockStorage\v2\Models\QuotaSet;
use OpenStack\BlockStorage\v2\Models\Snapshot;
use OpenStack\BlockStorage\v2\Models\Volume;
use OpenStack\BlockStorage\v2\Models\VolumeType;
use OpenStack\BlockStorage\v2\Service;
use OpenStack\Test\TestCase;

class ServiceTest extends TestCase
{
    /** @var Service */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;
        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_creates_volumes()
    {
        $opts = [
            "description"      => '1',
            "availabilityZone" => '2',
            "sourceVolumeId"   => '3',
            "snapshotId"       => '4',
            "size"             => 6,
            "name"             => '7',
            "imageId"          => '8',
            "volumeType"       => '9',
            "metadata"         => [
                'foo' => '1',
                'bar' => '2',
            ],
        ];

        $expectedJson = [
            'volume' => [
                "description"       => '1',
                "availability_zone" => '2',
                "source_volid"      => '3',
                "snapshot_id"       => '4',
                "size"              => 6,
                "name"              => '7',
                "imageRef"          => '8',
                "volume_type"       => '9',
                "metadata"          => [
                    'foo' => '1',
                    'bar' => '2',
                ],
            ],
        ];

        $this->mockRequest('POST', 'volumes', 'GET_volume', $expectedJson, []);

        self::assertInstanceOf(Volume::class, $this->service->createVolume($opts));
    }

    public function test_it_lists_volumes()
    {
        $this->mockRequest('GET', 'volumes', 'GET_volumes', null, []);
        $this->mockRequest(
            'GET',
            ['path' => 'volumes', 'query' => ['marker' => '5aa119a8-d25b-45a7-8d1b-88e127885635']],
            new Response(204),
            null,
            []
        );

        $count = 0;

        foreach ($this->service->listVolumes(false) as $volume) {
            $count++;
            self::assertInstanceOf(Volume::class, $volume);
        }

        self::assertEquals(2, $count);
    }

    public function test_it_gets_a_volume()
    {
        $volume = $this->service->getVolume('volumeId');

        self::assertInstanceOf(Volume::class, $volume);
        self::assertEquals('volumeId', $volume->id);
    }

    public function test_it_creates_volume_types()
    {
        $opts = ['name' => 'foo'];

        $expectedJson = ['volume_type' => $opts];

        $this->mockRequest('POST', 'types', 'GET_type', $expectedJson, []);

        self::assertInstanceOf(VolumeType::class, $this->service->createVolumeType($opts));
    }

    public function test_it_lists_volume_types()
    {
        $this->mockRequest('GET', 'types', 'GET_types', null, []);

        $count = 0;

        foreach ($this->service->listVolumeTypes() as $type) {
            $count++;
            self::assertInstanceOf(VolumeType::class, $type);
        }

        self::assertEquals(2, $count);
    }

    public function test_it_gets_a_volume_type()
    {
        $type = $this->service->getVolumeType('id');

        self::assertInstanceOf(VolumeType::class, $type);
        self::assertEquals('id', $type->id);
    }

    public function test_it_creates_snapshots()
    {
        $opts = [
            'name'        => 'snap-001',
            'description' => 'Daily backup',
            'volumeId'    => '5aa119a8-d25b-45a7-8d1b-88e127885635',
            'force'       => true,
        ];

        $expectedJson = ['snapshot' => [
            'name'        => $opts['name'],
            'description' => $opts['description'],
            'volume_id'   => $opts['volumeId'],
            'force'       => $opts['force'],
        ]];

        $this->mockRequest('POST', 'snapshots', 'GET_snapshot', $expectedJson, []);

        self::assertInstanceOf(Snapshot::class, $this->service->createSnapshot($opts));
    }

    public function test_it_lists_snapshots()
    {
        $this->mockRequest('GET', 'snapshots', 'GET_snapshots', null, []);
        $this->mockRequest(
            'GET',
            ['path' => 'snapshots', 'query' => ['marker' => 'e820db06-58b5-439d-bac6-c01faa3f6499']],
            new Response(204),
            null,
            []
        );

        $count = 0;

        foreach ($this->service->listSnapshots(false) as $snapshot) {
            $count++;
            self::assertInstanceOf(Snapshot::class, $snapshot);
        }

        self::assertEquals(2, $count);
    }

    public function test_it_gets_a_snapshot()
    {
        $snapshot = $this->service->getSnapshot('snapshotId');

        self::assertInstanceOf(Snapshot::class, $snapshot);
        self::assertEquals('snapshotId', $snapshot->id);
    }

    public function test_it_gets_quota_set()
    {
        $this->mockRequest('GET', 'os-quota-sets/tenant-id-1234', 'GET_quota_set', null, []);

        $quotaSet = $this->service->getQuotaSet('tenant-id-1234');

        self::assertInstanceOf(QuotaSet::class, $quotaSet);
        self::assertEquals(1, $quotaSet->gigabytes);
        self::assertEquals(2, $quotaSet->snapshots);
        self::assertEquals(3, $quotaSet->volumes);
    }
}
