<?php

namespace OpenStack\Sample\BlockStorage\v3;

use OpenStack\BlockStorage\v2\Models\VolumeType;
use OpenStack\Common\Error\BadResponseError;

class VolumeTypeTest extends TestCase
{
    public function testCreate(): VolumeType
    {
        $name = $this->randomStr();

        /** @var VolumeType $volumeType */
        require_once $this->sampleFile('volume_types/create.php', ['{name}' => $name]);

        $this->assertInstanceOf(VolumeType::class, $volumeType);
        $this->assertEquals($name, $volumeType->name);

        return $volumeType;
    }

    /**
     * @depends testCreate
     */
    public function testRead(VolumeType $createdVolumeType)
    {
        /** @var VolumeType $volumeType */
        require_once $this->sampleFile('volume_types/read.php', ['{volumeTypeId}' => $createdVolumeType->id]);

        $this->assertInstanceOf(VolumeType::class, $volumeType);
        $this->assertEquals($createdVolumeType->id, $volumeType->id);
        $this->assertEquals($createdVolumeType->name, $volumeType->name);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(VolumeType $createdVolumeType)
    {
        $newName = $this->randomStr();

        require_once $this->sampleFile('volume_types/update.php', [
            '{volumeTypeId}' => $createdVolumeType->id,
            '{newName}'      => $newName,
        ]);

        $createdVolumeType->retrieve();
        $this->assertEquals($newName, $createdVolumeType->name);
    }

    /**
     * @depends testCreate
     */
    public function testList(VolumeType $createdVolumeType)
    {
        $found = false;
        require_once $this->sampleFile(
            'volume_types/list.php',
            [
                '/** @var \OpenStack\BlockStorage\v2\Models\VolumeType $volumeType */' => <<<'PHP'
/** @var \OpenStack\BlockStorage\v2\Models\VolumeType $volumeType */
if ($volumeType->id === $createdVolumeType->id) {
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
    public function testDelete(VolumeType $createdVolumeType)
    {
        require_once $this->sampleFile('volume_types/delete.php', ['{volumeTypeId}' => $createdVolumeType->id]);

        foreach ($this->getService()->listVolumeTypes() as $volumeType) {
            if ($volumeType->id === $createdVolumeType->id) {
                $this->fail('Volume type still exists');
            }
        }

        $this->expectException(BadResponseError::class);
        $createdVolumeType->retrieve();
    }
}