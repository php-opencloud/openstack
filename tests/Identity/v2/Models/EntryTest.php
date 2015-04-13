<?php

namespace OpenStack\Test\Identity\v2\Models;

use OpenStack\Identity\v2\Models\Entry;
use OpenStack\Test\TestCase;

class EntryTest extends TestCase
{
    private $entry;

    public function setUp()
    {
        parent::setUp();

        $this->entry = new Entry($this->client->reveal());
    }

    public function test_null_is_returned_when_no_endpoints_are_found()
    {
        $this->assertNull($this->entry->getEndpointUrl('foo', 'bar'));
    }
} 