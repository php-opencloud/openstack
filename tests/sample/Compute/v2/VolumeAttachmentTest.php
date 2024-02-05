<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\BlockStorage\v2\Models\VolumeAttachment;
use OpenStack\BlockStorage\v3\Service;

class VolumeAttachmentTest extends TestCase
{
    public function testAttach(): VolumeAttachment
    {
        $server = $this->createServer();

        // let's wait for the server to be completely up
        // https://bugs.launchpad.net/nova/+bug/1998148
        // https://bugs.launchpad.net/nova/+bug/1960346
        sleep(15);

        $volume = $this->getCachedService(Service::class)->createVolume(
            [
                'name'        => $this->randomStr(),
                'description' => '',
                'size'        => 1,
            ]
        );
        $volume->waitUntil('available');
        $this->assertEquals('available', $volume->status);

        /** @var \OpenStack\BlockStorage\v2\Models\VolumeAttachment $volumeAttachment */
        require_once $this->sampleFile('volume_attachments/create.php', [
            '{serverId}' => $server->id,
            '{volumeId}' => $volume->id,
        ]);

        $this->assertInstanceOf(VolumeAttachment::class, $volumeAttachment);

        $volume->waitUntil('in-use');
        $this->assertEquals('in-use', $volume->status);

        return $volumeAttachment;
    }

    /**
     * @depends testAttach
     */
    public function testList(VolumeAttachment $createdVolumeAttachment)
    {
        $found = false;
        require_once $this->sampleFile(
            'volume_attachments/list.php',
            [
                '{serverId}'                                                                       => $createdVolumeAttachment->serverId,
                '/** @var \OpenStack\BlockStorage\v2\Models\VolumeAttachment $volumeAttachment */' => <<<'PHP'
/** @var \OpenStack\BlockStorage\v2\Models\VolumeAttachment $volumeAttachment */
if ($volumeAttachment->volumeId === $createdVolumeAttachment->volumeId) {
    $found = true;
}
PHP
                ,
            ]
        );
        $this->assertTrue($found);
    }

    /**
     * @depends testAttach
     */
    public function testDetach(VolumeAttachment $createdVolumeAttachment)
    {
        // let's wait for the server to be completely up
        // https://bugs.launchpad.net/nova/+bug/1998148
        // https://bugs.launchpad.net/nova/+bug/1960346
        sleep(15);

        require_once $this->sampleFile(
            'volume_attachments/delete.php',
            [
                '{serverId}' => $createdVolumeAttachment->serverId,
                '{volumeId}' => $createdVolumeAttachment->volumeId,
            ]
        );

        $volume = $this->getCachedService(Service::class)->getVolume($createdVolumeAttachment->volumeId);
        $volume->waitUntil('available', 240);
        $this->assertEquals('available', $volume->status);

        $server = $this->getService()->getServer(['id' => $createdVolumeAttachment->serverId]);
        foreach ($server->listVolumeAttachments() as $volumeAttachment) {
            $this->assertNotEquals($createdVolumeAttachment->id, $volumeAttachment->id);
        }

        $volume->delete();
        $this->deleteServer($server);
    }
}