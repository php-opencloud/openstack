<?php

namespace OpenStack\Test\BlockStorage\v3\FromV2\Models;

use OpenStack\BlockStorage\v3\Api;
use OpenStack\BlockStorage\v2\Models\Volume;

class VolumeTest extends \OpenStack\Test\BlockStorage\v2\Models\VolumeTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->volume = new Volume($this->client->reveal(), new Api());
        $this->volume->id = '1';
    }
}