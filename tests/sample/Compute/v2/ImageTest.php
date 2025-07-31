<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Compute\v2\Models\Server;

class ImageTest extends TestCase
{
    public function testCreate(): Image
    {
        $createdServer = $this->createServer();

        $name = $this->randomStr();
        $replacements = [
            '{serverId}'  => $createdServer->id,
            '{imageName}' => $name,
        ];

        require_once $this->sampleFile('images/create_server_image.php', $replacements);

        $createdImage = null;
        foreach ($this->getService()->listImages() as $image) {
            if ($image->name === $name) {
                $createdImage = $image;
                break;
            }
        }

        $this->assertNotNull($createdImage);
        $createdImage->retrieve();

        $createdImage->waitUntil('ACTIVE');
        $this->assertEquals('ACTIVE', $createdImage->status);

        $this->deleteServer($createdServer);
        return $createdImage;
    }

    /**
     * @depends testCreate
     */
    public function testList(Image $createdImage)
    {
        $found = false;
        require_once $this->sampleFile(
            'images/list_images.php',
            [
                '/** @var \OpenStack\Compute\v2\Models\Image $image */' => <<<'PHP'
/** @var \OpenStack\Compute\v2\Models\Image $image */
if ($image->id === $createdImage->id) {
    $found = true;
}
PHP
                ,
            ]
        );
        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     */
    public function testGet(Image $createdImage)
    {
        /** @var \OpenStack\Compute\v2\Models\Image $image */
        require_once $this->sampleFile('images/get_image.php', ['{imageId}' => $createdImage->id]);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals($createdImage->id, $image->id);
        $this->assertEquals($createdImage->name, $image->name);
    }

    /**
     * @depends testCreate
     */
    public function testMergeMetadata(Image $createdImage)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();

        $createdImage->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'images/merge_image_metadata.php',
            [
                '{imageId}' => $createdImage->id,
                '{key}'     => 'Foo',
                '{value}'   => $fooVal,
            ]
        );

        $metadata = $createdImage->getMetadata();
        $this->assertEquals($initVal, $metadata['Init']);
        $this->assertEquals($fooVal, $metadata['Foo']);
    }

    /**
     * @depends testCreate
     * @depends testMergeMetadata
     */
    public function testGetMetadata(Image $createdImage)
    {
        /** @var array $metadata */
        require_once $this->sampleFile('images/retrieve_image_metadata.php', ['{imageId}' => $createdImage->id]);

        $this->assertArrayHasKey('Init', $metadata);
        $this->assertArrayHasKey('Foo', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testResetMetadata(Image $createdImage)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();

        $createdImage->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'images/reset_image_metadata.php',
            [
                '{imageId}' => $createdImage->id,
                '{key}'     => 'Foo',
                '{value}'   => $fooVal,
            ]
        );

        $metadata = $createdImage->getMetadata();
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertArrayNotHasKey('Init', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testDeleteMetadata(Image $createdImage)
    {
        $createdImage->mergeMetadata(['Init' => $this->randomStr(), 'Init2' => $this->randomStr()]);

        require_once $this->sampleFile(
            'images/delete_image_metadata_item.php',
            [
                '{imageId}' => $createdImage->id,
                '{key}'     => 'Init',
            ]
        );

        $metadata = $createdImage->getMetadata();
        $this->assertArrayNotHasKey('Init', $metadata);
        $this->assertArrayHasKey('Init2', $metadata);
    }


    /**
     * @depends testCreate
     */
    public function testDelete(Image $createdImage)
    {
        require_once $this->sampleFile('images/delete_image.php', ['{imageId}' => $createdImage->id]);

        foreach ($this->getService()->listImages() as $image) {
            $this->assertNotEquals($createdImage->id, $image->id);
        }

        $this->expectException(BadResponseError::class);
        $createdImage->retrieve();
    }
}