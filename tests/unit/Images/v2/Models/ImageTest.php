<?php

namespace OpensTack\Test\Images\v2\Models;

use OpenStack\Images\v2\Api;
use OpenStack\Images\v2\Models\Image;
use OpenStack\Test\TestCase;

class ImageTest extends TestCase
{
    /** @var Image */
    private $image;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->image = new Image($this->client->reveal(), new Api());
    }

    public function test_it_retrieves()
    {
        $this->client->getConfig('base_uri')->shouldBeCalled()->willReturn(\GuzzleHttp\Psr7\uri_for(''));

        $this->setupMock('GET', 'images/id', null, [], 'GET_image');

        $this->image->id = 'id';
        $this->image->retrieve();

        $this->assertEquals("active", $this->image->status);
        $this->assertEquals("cirros-0.3.2-x86_64-disk", $this->image->name);
        $this->assertEquals([], $this->image->tags);
        $this->assertEquals("bare", $this->image->containerFormat);
        $this->assertEquals(new \DateTimeImmutable("2014-05-05T17:15:10Z"), $this->image->createdAt);
        $this->assertEquals("qcow2", $this->image->diskFormat);
        $this->assertEquals(new \DateTimeImmutable("2014-05-05T17:15:11Z"), $this->image->updatedAt);
        $this->assertEquals("public", $this->image->visibility);
        $this->assertEquals(0, $this->image->minDisk);
        $this->assertFalse($this->image->protected);
        $this->assertEquals("1bea47ed-f6a9-463b-b423-14b9cca9ad27", $this->image->id);
        $this->assertEquals("64d7c1cd2b6f60c92c14662941cb7913", $this->image->checksum);
        $this->assertEquals("5ef70662f8b34079a6eddb8da9d75fe8", $this->image->ownerId);
        $this->assertEquals(13167616, $this->image->size);
        $this->assertEquals(0, $this->image->minRam);
        $this->assertNull($this->image->virtualSize);
    }
}