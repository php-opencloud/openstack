<?php

namespace OpenStack\Test\ObjectStore\v1\Models;

use OpenStack\ObjectStore\v1\Api;
use OpenStack\ObjectStore\v1\Models\Container;
use OpenStack\Test\TestCase;

class ContainerTest extends TestCase
{
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->container = new Container($this->client->reveal(), new Api());
    }

    public function testSomething()
    {
    }
}