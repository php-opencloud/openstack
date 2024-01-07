<?php

namespace OpenStack\Test\BlockStorage\v3\FromV2\Models;

use OpenStack\BlockStorage\v3\Api;
use OpenStack\BlockStorage\v2\Models\QuotaSet;

class QuotaSetTest extends \OpenStack\Test\BlockStorage\v2\Models\QuotaSetTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->quotaSet = new QuotaSet($this->client->reveal(), new Api());
        $this->quotaSet->tenantId = 'tenant-foo';
    }
}