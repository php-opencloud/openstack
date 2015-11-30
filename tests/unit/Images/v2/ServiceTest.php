<?php

namespace OpenStack\Test\Images\v2;

use OpenStack\Images\v2\Api;
use OpenStack\Images\v2\Models\Image;
use OpenStack\Images\v2\Service;
use OpenStack\Test\TestCase;

class ServiceTest extends TestCase
{
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = __DIR__;

        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_creates_image()
    {
        $this->client
            ->getConfig('base_uri')
            ->shouldBeCalled()
            ->willReturn(\GuzzleHttp\Psr7\uri_for(''));

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

        $this->setupMock('POST', 'images', $expectedJson, [], 'GET_image');

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
        $this->client
            ->getConfig('base_uri')
            ->shouldBeCalled()
            ->willReturn(\GuzzleHttp\Psr7\uri_for(''));

        $this->client
            ->request('GET', 'images', ['query' => ['limit' => 5], 'headers' => []])
            ->shouldBeCalled()
            ->willReturn($this->getFixture('GET_images'));

        foreach ($this->service->listImages(['limit' => 5]) as $image) {
            $this->assertInstanceOf(Image::class, $image);
        }
    }

    public function test_it_gets_image()
    {
        $this->assertInstanceOf(Image::class, $this->service->getImage('id'));
    }

    public function test_it_updates_image()
    {
        $image = new Image($this->client->reveal(), new Api());

        $opts = [
            'architecture'    => 'x86_64',
            'containerFormat' => 'ami',
            'diskFormat'      => 'iso',
            'minDisk'         => 1,
            'minRam'          => 1,
            'name'            => 'foo',
            'osDistro'        => 'ubuntu',
            'osVersion'       => '12.10',
            'owner'           => 'bar',
            'protected'       => true,
            'size'            => 10,
            'tags'            => ['1', '2', '3'],
            'visibility'      => 'public',
        ];

        $expectedJson = [
            ['op' => 'replace', 'path' => 'architecture', 'value' => 'x86_64'],
            ['op' => 'replace', 'path' => 'containerFormat', 'value' => 'ami'],
            ['op' => 'replace', 'path' => 'diskFormat', 'value' => 'iso'],
            ['op' => 'replace', 'path' => 'minDisk', 'value' => 1],
            ['op' => 'replace', 'path' => 'minRam', 'value' => 1],
            ['op' => 'replace', 'path' => 'name', 'value' => 'foo'],
            ['op' => 'replace', 'path' => 'osDistro', 'value' => 'ubuntu'],
            ['op' => 'replace', 'path' => 'osVersion', 'value' => '12.10'],
            ['op' => 'replace', 'path' => 'owner', 'value' => 'bar'],
            ['op' => 'replace', 'path' => 'protected', 'value' => true],
            ['op' => 'replace', 'path' => 'size', 'value' => 10],
            ['op' => 'replace', 'path' => 'tags', 'value' => ['1', '2', '3']],
            ['op' => 'replace', 'path' => 'visibility', 'value' => 'public'],
        ];

        $headers = ['Content-Type' => 'application/openstack-images-v2.1-json-patch'];
        $this->setupMock('PATCH', 'images/id', $expectedJson, $headers, 'POST_image');

        $image->update($opts);
    }
}