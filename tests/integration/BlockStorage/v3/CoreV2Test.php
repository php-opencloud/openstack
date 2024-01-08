<?php

namespace OpenStack\Integration\BlockStorage\v3;

use OpenStack\BlockStorage\v2\Models\Snapshot;
use OpenStack\BlockStorage\v2\Models\Volume;
use OpenStack\BlockStorage\v2\Models\VolumeType;
use OpenStack\BlockStorage\v2\Service;
use OpenStack\Integration\TestCase;
use OpenStack\Integration\Utils;

class CoreV2Test extends TestCase
{
    protected $service;

    protected function getService() : Service
    {
        if (null === $this->service) {
            $this->service = Utils::getOpenStack()->blockStorageV3();
        }

        return $this->service;
    }

    public function runTests()
    {
        $this->startTimer();

        $this->logger->info('-> Volumes');
        $this->volumes();
        $this->logger->info('-> Volume Types');
        $this->volumeTypes();
        $this->logger->info('-> Snapshots');
        $this->snapshots();
        $this->logger->info('-> Snapshot list');
        $this->snapshotList();

        $this->outputTimeTaken();
    }

    public function volumes()
    {
        $this->logStep('Creating volume type');
        $volumeType = $this->getService()->createVolumeType(['name' => $this->randomStr()]);

        $replacements = [
            '{description}' => $this->randomStr(),
            "'{size}'" => 1,
            '{name}' => $this->randomStr(),
            '{volumeType}' => $volumeType->id,
            '{key1}' => $this->randomStr(),
            '{val1}' => $this->randomStr(),
        ];

        $this->logStep('Creating volume');
        /** @var Volume $volume */
        require_once $this->sampleFile('volumes/create.php', $replacements);
        self::assertInstanceOf(Volume::class, $volume);
        self::assertEquals($replacements['{name}'], $volume->name);
        self::assertEquals(1, $volume->size);
        self::assertEquals($volumeType->name, $volume->volumeTypeName);

        $volumeId = $volume->id;
        $replacements = ['{volumeId}' => $volumeId];

        $this->logStep('Getting volume');
        /** @var Volume $volume */
        require_once $this->sampleFile('volumes/get.php', $replacements);
        self::assertInstanceOf(Volume::class, $volume);

        $replacements += [
            '{newName}' => $this->randomStr(),
            '{newDescription}' => $this->randomStr(),
        ];

        $this->logStep('Updating volume');
        /** @var Volume $volume */
        require_once $this->sampleFile('volumes/update.php', $replacements);
        self::assertInstanceOf(Volume::class, $volume);

        $this->logStep('Listing volumes');
        /** @var \Generator $volumes */
        require_once $this->sampleFile('volumes/list.php', $replacements);

        $volume = $this->getService()->getVolume($volumeId);
        $volume->waitUntil('available');

        $this->logStep('Deleting volume');
        require_once $this->sampleFile('volumes/delete.php', $replacements);

        $volume = $this->getService()->getVolume($volumeId);
        $volume->waitUntilDeleted();

        $this->logStep('Deleting volume type');
        $volumeType->delete();
    }

    public function volumeTypes()
    {
        $replacements = [
            '{name}' => $this->randomStr(),
        ];

        $this->logStep('Creating volume type');
        /** @var VolumeType $volumeType */
        require_once $this->sampleFile('volume_types/create.php', $replacements);
        self::assertInstanceOf(VolumeType::class, $volumeType);
        self::assertEquals($replacements['{name}'], $volumeType->name);

        $replacements = ['{volumeTypeId}' => $volumeType->id];

        $this->logStep('Getting volume type');
        /** @var VolumeType $volumeType */
        require_once $this->sampleFile('volume_types/get.php', $replacements);
        self::assertInstanceOf(VolumeType::class, $volumeType);

        $replacements += ['{newName}' => $this->randomStr()];

        $this->logStep('Updating volume type');
        /** @var VolumeType $volumeType */
        require_once $this->sampleFile('volume_types/update.php', $replacements);
        self::assertInstanceOf(VolumeType::class, $volumeType);

        $this->logStep('Listing volume types');
        /** @var \Generator $volumeTypes */
        require_once $this->sampleFile('volume_types/list.php', $replacements);

        $this->logStep('Deleting volume type');
        require_once $this->sampleFile('volume_types/delete.php', $replacements);
    }

    public function snapshots()
    {
        $this->logStep('Creating volume');
        $volume = $this->getService()->createVolume(['name' => $this->randomStr(), 'size' => 1]);
        $volume->waitUntil('available', 60);

        $replacements = [
            '{volumeId}' => $volume->id,
            '{name}' => $this->randomStr(),
            '{description}' => $this->randomStr(),
        ];

        $this->logStep('Creating snapshot');
        /** @var Snapshot $snapshot */
        require_once $this->sampleFile('snapshots/create.php', $replacements);
        self::assertInstanceOf(Snapshot::class, $snapshot);
        self::assertEquals($replacements['{name}'], $snapshot->name);
        $volume->waitUntil('available', 60);

        $snapshotId = $snapshot->id;
        $replacements = ['{snapshotId}' => $snapshotId];

        $this->logStep('Getting snapshot');
        /** @var Snapshot $snapshot */
        require_once $this->sampleFile('snapshots/get.php', $replacements);
        self::assertInstanceOf(Snapshot::class, $snapshot);

        $this->getService()
            ->getSnapshot($snapshot->id)
            ->mergeMetadata(['key1' => 'val1']);

        $replacements += ['{key}' => 'key2', '{val}' => 'val2'];
        $this->logStep('Adding metadata');
        require_once $this->sampleFile('snapshots/merge_metadata.php', $replacements);

        $this->logStep('Retrieving metadata');
        /** @var array $metadata */
        require_once $this->sampleFile('snapshots/get_metadata.php', $replacements);
        self::assertEquals(['key1' => 'val1', 'key2' => 'val2'], $metadata);

        $replacements = ['{snapshotId}' => $snapshot->id, '{key}' => 'key3', '{val}' => 'val3'];
        $this->logStep('Resetting metadata');
        require_once $this->sampleFile('snapshots/reset_metadata.php', $replacements);

        $this->logStep('Retrieving metadata');
        /** @var array $metadata */
        require_once $this->sampleFile('snapshots/get_metadata.php', $replacements);
        self::assertEquals(['key3' => 'val3'], $metadata);

        $replacements += ['{newName}' => $this->randomStr(), '{newDescription}' => $this->randomStr()];
        $this->logStep('Updating snapshot');
        require_once $this->sampleFile('snapshots/update.php', $replacements);

        $snapshot->waitUntil('available', 60);

        $this->logStep('Listing snapshots');
        require_once $this->sampleFile('snapshots/list.php', $replacements);

        $this->logStep('Deleting snapshot');
        require_once $this->sampleFile('snapshots/delete.php', $replacements);
        $snapshot->waitUntilDeleted();

        $this->logStep('Deleting volume');
        $volume->delete();
    }

    public function snapshotList()
    {
        $this->logStep('Creating volume');
        $volume = $this->getService()->createVolume(['name' => $this->randomStr(), 'size' => 1]);
        $volume->waitUntil('available', 60);

        $names = ['b' . $this->randomStr(), 'a' . $this->randomStr(), 'd' . $this->randomStr(), 'c' . $this->randomStr()];
        $createdSnapshots = [];
        foreach ($names as $name) {
            $this->logStep('Creating snapshot ' . $name);
            $snapshot = $this->getService()->createSnapshot([
                'volumeId' => $volume->id,
                'name' => $name,
            ]);

            self::assertInstanceOf(Snapshot::class, $snapshot);

            $createdSnapshots[] = $snapshot;
            $snapshot->waitUntil('available', 60);
        }

        try {
            $replacements = [
                '{sortKey}' => 'display_name',
                '{sortDir}' => 'asc',
            ];

            $this->logStep('Listing snapshots');
            require_once $this->sampleFile('snapshots/list.php', $replacements);

            $this->logStep('Listing snapshots sorted asc');
            /** @var Snapshot $snapshot */
            require_once $this->sampleFile('snapshots/list_sorted.php', $replacements);
            self::assertInstanceOf(Snapshot::class, $snapshot);
            self::assertEquals($names[2], $snapshot->name);

            $this->logStep('Listing snapshots sorted desc');
            $replacements['{sortDir}'] = 'desc';
            /** @var Snapshot $snapshot */
            require_once $this->sampleFile('snapshots/list_sorted.php', $replacements);
            self::assertInstanceOf(Snapshot::class, $snapshot);
            self::assertEquals($names[1], $snapshot->name);
        } finally {
            foreach ($createdSnapshots as $snapshot) {
                $this->logStep('Deleting snapshot ' . $snapshot->name);
                $snapshot->delete();
                $snapshot->waitUntilDeleted();
            }

            $this->logStep('Deleting volume');
            $volume->delete();
        }
    }

}