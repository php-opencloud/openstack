<?php

namespace OpenStack\Test\BlockStorage\v3\FromV2\Models;

use OpenStack\BlockStorage\v3\Api;
use OpenStack\BlockStorage\v2\Models\Snapshot;

class SnapshotTest extends \OpenStack\Test\BlockStorage\v2\Models\SnapshotTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->snapshot = new Snapshot($this->client->reveal(), new Api());
        $this->snapshot->id = '1';
    }
}