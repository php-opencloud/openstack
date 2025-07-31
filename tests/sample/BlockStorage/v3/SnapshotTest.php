<?php

namespace OpenStack\Sample\BlockStorage\v3;

use OpenStack\BlockStorage\v2\Models\Snapshot;
use OpenStack\Common\Error\BadResponseError;

class SnapshotTest extends TestCase
{
    public function testCreate(): Snapshot
    {
        $volume = $this->getService()->createVolume(['name' => $this->randomStr(), 'size' => 1]);
        $volume->waitUntil('available');

        $name = $this->randomStr();
        $description = $this->randomStr();

        /** @var Snapshot $snapshot */
        require_once $this->sampleFile('snapshots/create.php', [
            '{volumeId}'    => $volume->id,
            '{name}'        => $name,
            '{description}' => $description,
        ]);

        $this->assertInstanceOf(Snapshot::class, $snapshot);
        $this->assertEquals($name, $snapshot->name);
        $this->assertEquals($description, $snapshot->description);

        $snapshot->waitUntil('available');

        return $snapshot;
    }

    /**
     * @depends testCreate
     */
    public function testGet(Snapshot $createdSnapshot)
    {
        /** @var Snapshot $snapshot */
        require_once $this->sampleFile('snapshots/get.php', ['{snapshotId}' => $createdSnapshot->id]);

        $this->assertInstanceOf(Snapshot::class, $snapshot);
        $this->assertEquals($createdSnapshot->id, $snapshot->id);
        $this->assertNull($snapshot->name);

        $snapshot->retrieve();
        $this->assertEquals($createdSnapshot->name, $snapshot->name);
    }

    /**
     * @depends testCreate
     */
    public function testMergeMetadata(Snapshot $createdSnapshot)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();

        $createdSnapshot->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'snapshots/merge_metadata.php',
            [
                '{snapshotId}' => $createdSnapshot->id,
                '{key}'        => 'Foo',
                '{val}'        => $fooVal,
            ]
        );

        $metadata = $createdSnapshot->getMetadata();
        $this->assertEquals($initVal, $metadata['Init']);
        $this->assertEquals($fooVal, $metadata['Foo']);
    }

    /**
     * @depends testCreate
     * @depends testMergeMetadata
     */
    public function testGetMetadata(Snapshot $createdSnapshot)
    {
        /** @var array $metadata */
        require_once $this->sampleFile('snapshots/get_metadata.php', ['{snapshotId}' => $createdSnapshot->id]);

        $this->assertArrayHasKey('Init', $metadata);
        $this->assertArrayHasKey('Foo', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testResetMetadata(Snapshot $createdSnapshot)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();

        $createdSnapshot->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'snapshots/reset_metadata.php',
            [
                '{snapshotId}' => $createdSnapshot->id,
                '{key}'        => 'Foo',
                '{val}'        => $fooVal,
            ]
        );

        $metadata = $createdSnapshot->getMetadata();
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertArrayNotHasKey('Init', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Snapshot $createdSnapshot)
    {
        $newName = $this->randomStr();
        $newDescription = $this->randomStr();

        require_once $this->sampleFile('snapshots/update.php', [
            '{snapshotId}' => $createdSnapshot->id,
            '{newName}' => $newName,
            '{newDescription}' => $newDescription,
        ]);

        $createdSnapshot->retrieve();
        $this->assertEquals($newName, $createdSnapshot->name);
        $this->assertEquals($newDescription, $createdSnapshot->description);
    }

    /**
     * @depends testCreate
     */
    public function testList(Snapshot $createdSnapshot)
    {
        $found = false;
        require_once $this->sampleFile(
            'snapshots/list.php',
            [
                '/** @var \OpenStack\BlockStorage\v2\Models\Snapshot $snapshot */' => <<<'PHP'
/** @var \OpenStack\BlockStorage\v2\Models\Snapshot $snapshot */
if ($snapshot->id === $createdSnapshot->id) {
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
    public function testDelete(Snapshot $createdSnapshot)
    {
        $volume = $this->getService()->getVolume($createdSnapshot->volumeId);
        require_once $this->sampleFile('snapshots/delete.php', ['{snapshotId}' => $createdSnapshot->id]);
        $createdSnapshot->waitUntilDeleted();

        $volume->delete();
        $volume->waitUntilDeleted();

        $this->expectException(BadResponseError::class);
        $createdSnapshot->retrieve();
    }

    public function testListAsc()
    {
        $volume = $this->getService()->createVolume(['name' => $this->randomStr(), 'size' => 1]);
        $volume->waitUntil('available');

        $postfix = $this->randomStr();
        $names = ['b' . $postfix, 'a' . $postfix, 'c' . $postfix];
        $createdSnapshots = [];
        foreach ($names as $name) {
            $snapshot = $this->getService()->createSnapshot([
                'volumeId' => $volume->id,
                'name' => $name,
            ]);

            $this->assertInstanceOf(Snapshot::class, $snapshot);

            $createdSnapshots[] = $snapshot;
        }

        foreach ($createdSnapshots as $snapshot) {
            $snapshot->waitUntil('available');
        }

        try {
            $rightOrder = ['a' . $postfix, 'b' . $postfix, 'c' . $postfix];
            $i = 0;
            require_once $this->sampleFile('snapshots/list_sorted.php', [
                '{sortKey}' => 'display_name',
                '{sortDir}' => 'asc',
                '/** @var \OpenStack\BlockStorage\v2\Models\Snapshot $snapshot */' => <<<'PHP'
/** @var \OpenStack\BlockStorage\v2\Models\Snapshot $snapshot */
if ($snapshot->name === $rightOrder[$i]) {
    $i++;
}
PHP
                ,
            ]);
            $this->assertEquals(3, $i);
        } finally {
            foreach ($createdSnapshots as $snapshot) {
                $snapshot->delete();
            }

            foreach ($createdSnapshots as $snapshot) {
                $snapshot->waitUntilDeleted();
            }

            $volume->delete();
            $volume->waitUntilDeleted();
        }
    }

    public function testListDesc()
    {
        $volume = $this->getService()->createVolume(['name' => $this->randomStr(), 'size' => 1]);
        $volume->waitUntil('available');

        $postfix = $this->randomStr();
        $names = ['b' . $postfix, 'a' . $postfix, 'c' . $postfix];
        $createdSnapshots = [];
        foreach ($names as $name) {
            $snapshot = $this->getService()->createSnapshot([
                'volumeId' => $volume->id,
                'name' => $name,
            ]);

            $this->assertInstanceOf(Snapshot::class, $snapshot);

            $createdSnapshots[] = $snapshot;
        }

        foreach ($createdSnapshots as $snapshot) {
            $snapshot->waitUntil('available');
        }

        try {
            $rightOrder = ['c' . $postfix, 'b' . $postfix, 'a' . $postfix];
            $i = 0;
            require_once $this->sampleFile('snapshots/list_sorted.php', [
                '{sortKey}' => 'display_name',
                '{sortDir}' => 'desc',
                '/** @var \OpenStack\BlockStorage\v2\Models\Snapshot $snapshot */' => <<<'PHP'
/** @var \OpenStack\BlockStorage\v2\Models\Snapshot $snapshot */
if ($snapshot->name === $rightOrder[$i]) {
    $i++;
}
PHP
                ,
            ]);
            $this->assertEquals(3, $i);
        } finally {
            foreach ($createdSnapshots as $snapshot) {
                $snapshot->delete();
            }

            foreach ($createdSnapshots as $snapshot) {
                $snapshot->waitUntilDeleted();
            }

            $volume->delete();
            $volume->waitUntilDeleted();
        }
    }
}