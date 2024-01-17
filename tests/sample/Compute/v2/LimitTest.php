<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Compute\v2\Models\Limit;

class LimitTest extends TestCase
{
    public function testGet()
    {
        /** @var \OpenStack\Compute\v2\Models\Limit $limit */
        require_once $this->sampleFile('limits/get_limits.php', []);

        $this->assertInstanceOf(Limit::class, $limit);
    }
}