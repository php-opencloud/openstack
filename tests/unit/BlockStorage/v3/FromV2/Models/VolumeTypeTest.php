<?php

namespace OpenStack\Test\BlockStorage\v3\FromV2\Models;

use OpenStack\BlockStorage\v3\Api;
use OpenStack\BlockStorage\v2\Models\VolumeType;

class VolumeTypeTest extends \OpenStack\Test\BlockStorage\v2\Models\VolumeTypeTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->volumeType = new VolumeType($this->client->reveal(), new Api());
        $this->volumeType->id = '1';
    }
}