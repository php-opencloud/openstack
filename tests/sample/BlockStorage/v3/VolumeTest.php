<?php

namespace OpenStack\Sample\BlockStorage\v3;

use OpenStack\BlockStorage\v2\Models\Volume;

class VolumeTest extends TestCase
{
    public function testCreate(): Volume
    {
        $name = $this->randomStr();

        $volumeType = $this->getService()->createVolumeType(['name' => $this->randomStr()]);

        /** @var Volume $volume */
        require_once $this->sampleFile('volumes/create.php', [
            '{description}' => $this->randomStr(),
            "'{size}'"      => 1,
            '{name}'        => $name,
            '{volumeType}'  => $volumeType->id,
            '{key1}'        => $this->randomStr(),
            '{val1}'        => $this->randomStr(),
        ]);

        $this->assertInstanceOf(Volume::class, $volume);
        $this->assertEquals($name, $volume->name);
        $this->assertEquals(1, $volume->size);
        $this->assertEquals($volumeType->name, $volume->volumeTypeName);

        $volume->waitUntil('available');

        return $volume;
    }

    /**
     * @depends testCreate
     */
    public function testRead(Volume $createdVolume)
    {
        /** @var Volume $volume */
        require_once $this->sampleFile('volumes/read.php', [
            '{volumeId}' => $createdVolume->id,
        ]);

        $this->assertInstanceOf(Volume::class, $volume);
        $this->assertEquals($createdVolume->id, $volume->id);
        $this->assertEquals($createdVolume->name, $volume->name);
        $this->assertEquals($createdVolume->size, $volume->size);
        $this->assertEquals($createdVolume->description, $volume->description);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Volume $createdVolume)
    {
        $newName = $this->randomStr();
        $newDescription = $this->randomStr();

        require_once $this->sampleFile('volumes/update.php', [
            '{volumeId}'       => $createdVolume->id,
            '{newName}'        => $newName,
            '{newDescription}' => $newDescription,
        ]);

        $createdVolume->retrieve();
        $this->assertEquals($newName, $createdVolume->name);
        $this->assertEquals($newDescription, $createdVolume->description);
    }

    /**
     * @depends testCreate
     */
    public function testList(Volume $createdVolume)
    {
        $found = false;
        require_once $this->sampleFile(
            'volumes/list.php',
            [
                '/** @var \OpenStack\BlockStorage\v2\Models\Volume $volume */' => <<<'PHP'
/** @var \OpenStack\BlockStorage\v2\Models\Volume $volume */
if ($volume->id === $createdVolume->id) {
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
    public function testDelete(Volume $createdVolume)
    {
        $volumeTypeName = $createdVolume->volumeTypeName;

        require_once $this->sampleFile('volumes/delete.php', [
            '{volumeId}' => $createdVolume->id,
        ]);
        $createdVolume->waitUntilDeleted();

        $found = false;
        foreach ($this->getService()->listVolumes() as $volume) {
            if ($volume->id === $createdVolume->id) {
                $found = true;
            }
        }
        $this->assertFalse($found);

        foreach ($this->getService()->listVolumeTypes() as $volumeType) {
            if ($volumeType->name === $volumeTypeName) {
                $volumeType->delete();
            }
        }
    }
}