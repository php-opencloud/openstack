<?php

namespace OpenStack\Test\ObjectStore\v1\Models;

use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Object;
use OpenStack\Test\TestCase;

class ObjectTest extends TestCase
{
    private $object;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->object = new Object($this->client->reveal(), new Api());
    }

    public function testSomething()
    {
    }
}