<?php

namespace OpenStack\Sample\BlockStorage\v2;

use OpenStack\BlockStorage\v3\Service;

class VolumeTest extends \OpenStack\Sample\BlockStorage\v3\VolumeTest
{
    use ConvertFromV3;

    public function testUsingV2()
    {
        $this->assertNotInstanceOf(Service::class, $this->getService());
    }
}