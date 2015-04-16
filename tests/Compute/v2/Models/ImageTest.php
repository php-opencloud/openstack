<?php

namespace OpenStack\Test\Compute\v2\Models;

use GuzzleHttp\Message\Response;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Test\TestCase;

class ImageTest extends TestCase
{
    private $image;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->image = new Image($this->client->reveal(), new Api());
        $this->image->id = 'imageId';
    }

    public function test_it_retrieves()
    {

    }

    public function test_it_deletes()
    {
        $request = $this->setupMockRequest('DELETE', 'images/imageId');
        $this->setupMockResponse($request, new Response(204));

        $this->image->delete();
    }

    public function test_it_retrieves_metadata()
    {

    }

    public function test_it_sets_metadata()
    {

    }

    public function test_it_updates_metadata()
    {

    }

    public function test_it_retrieves_a_metadata_item()
    {

    }

    public function test_it_deletes_a_metadata_item()
    {

    }
}
