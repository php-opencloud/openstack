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

    public function test_it_gets_image()
    {
        self::assertInstanceOf(Image::class, $this->service->getImage('id'));
    }
}
