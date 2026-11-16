<?php

namespace OpenStack\Test\Images\v2;

use GuzzleHttp\Psr7\Uri;
use OpenStack\Images\v2\Api;
use OpenStack\Images\v2\Models\Image;
use OpenStack\Images\v2\Service;
use OpenStack\Test\TestCase;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_creates_image()
    {
        $returnedUri = function_exists('\GuzzleHttp\Psr7\uri_for')
            ? \GuzzleHttp\Psr7\uri_for('')
            : \GuzzleHttp\Psr7\Utils::uriFor('');

        $this->client
            ->getConfig('base_uri')
            ->shouldBeCalled()
            ->willReturn($returnedUri);

        $expectedJson = [
            "name" => "Ubuntu 12.10",
            "tags" => [
                "ubuntu",
                "quantal"
            ],
            "container_format" => "bare",
            "disk_format" => "qcow2",
            "visibility" => "private",
            "min_disk" => 0,
            "protected" => false,
            "min_ram" => 0,
        ];

        $this->mockRequest('POST', 'v2/images', 'GET_image', $expectedJson, []);

        $this->service->createImage([
            'name' => 'Ubuntu 12.10',
            'tags' => ['ubuntu', 'quantal'],
            'containerFormat' => 'bare',
            'diskFormat' => 'qcow2',
            'visibility' => 'private',
            'minDisk'    => 0,
            'protected'  => false,
            'minRam'     => 0,
        ]);
    }

    public function test_it_lists_images()
    {
        $returnedUri = function_exists('\GuzzleHttp\Psr7\uri_for')
            ? \GuzzleHttp\Psr7\uri_for('')
            : \GuzzleHttp\Psr7\Utils::uriFor('');

        $this->client
            ->getConfig('base_uri')
            ->shouldBeCalled()
            ->willReturn($returnedUri);

        $this->mockRequest('GET', ['path' => 'v2/images', 'query' => ['limit' => 5]], 'GET_images');

        foreach ($this->service->listImages(['limit' => 5]) as $image) {
            self::assertInstanceOf(Image::class, $image);
        }
    }

    public function test_it_lists_images_from_every_page_until_one_has_no_next_link()
    {
        $returnedUri = function_exists('\GuzzleHttp\Psr7\uri_for')
            ? \GuzzleHttp\Psr7\uri_for('')
            : \GuzzleHttp\Psr7\Utils::uriFor('');

        $this->client
            ->getConfig('base_uri')
            ->shouldBeCalled()
            ->willReturn($returnedUri);

        // Glance cuts the page to its api_limit_max (2 in the fixture) whatever limit was asked for
        $this->mockRequest('GET', ['path' => 'v2/images', 'query' => ['limit' => 1000]], 'GET_images_page1');
        $this->mockRequest(
            'GET',
            ['path' => 'v2/images', 'query' => ['limit' => 1000, 'marker' => '781b3762-9469-4cec-b58d-3349e5de4e9c']],
            'GET_images_page2'
        );

        $ids = [];
        foreach ($this->service->listImages(['limit' => 1000]) as $image) {
            $ids[] = $image->id;
        }

        self::assertSame(
            [
                '1bea47ed-f6a9-463b-b423-14b9cca9ad27',
                '781b3762-9469-4cec-b58d-3349e5de4e9c',
                '5d3c1c62-8f3e-4d2b-9c41-0a7b6e2f1d93',
            ],
            $ids
        );
    }

    public function test_it_gets_image()
    {
        self::assertInstanceOf(Image::class, $this->service->getImage('id'));
    }
}
