<?php

namespace OpenStack\Test\BlockStorage\v3\FromV2;

use OpenStack\BlockStorage\v2\Service;
use OpenStack\BlockStorage\v3\Api;

class ServiceTest extends \OpenStack\Test\BlockStorage\v2\ServiceTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new Service($this->client->reveal(), new Api());
    }
}
