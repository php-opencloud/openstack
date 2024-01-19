<?php

namespace OpenStack\Sample\Images\v2;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Images\v2\Models\Image;

class ImageTest extends TestCase
{
    public function testCreate(): Image
    {
        /** @var Image $image */

        require_once $this->sampleFile(
            'images/create.php',
            [
                '{name}'            => $this->randomStr(),
                '{tag1}'            => 'ubuntu',
                '{tag2}'            => 'test',
                '{containerFormat}' => 'bare',
                '{diskFormat}'      => 'qcow2',
                '{visibility}'      => 'private',
            ]
        );
        $this->assertInstanceOf(Image::class, $image);

        return $image;
    }

    /**
     * @depends testCreate
     */
    public function testList(Image $createdImage)
    {
        $found = false;
        require_once $this->sampleFile(
            'images/list.php',
            [
                '{imageId}'                                            => $createdImage->id,
                '/** @var \OpenStack\Images\v2\Models\Image $image */' => <<<'PHP'
/** @var \OpenStack\Images\v2\Models\Image $image */
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
    public function testRead(Image $createdImage)
    {
        /** @var Image $image */
        require_once $this->sampleFile(
            'images/read.php',
            [
                '{imageId}' => $createdImage->id,
            ]
        );
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals($createdImage->id, $image->id);
        $this->assertEquals($createdImage->name, $image->name);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Image $createdImage)
    {
        $newName = $this->randomStr();

        /** @var Image $image */
        require_once $this->sampleFile(
            'images/update.php',
            [
                '{imageId}'    => $createdImage->id,
                '{name}'       => $newName,
                '{visibility}' => 'private',
            ]
        );
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals($newName, $image->name);
        $this->assertEquals('private', $image->visibility);

        $createdImage->retrieve();
        $this->assertEquals($newName, $createdImage->name);
        $this->assertEquals('private', $createdImage->visibility);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Image $createdImage)
    {
        require_once $this->sampleFile(
            'images/delete.php',
            [
                '{imageId}' => $createdImage->id,
            ]
        );

        $found = false;
        foreach ($this->getService()->listImages() as $image) {
            if ($image->id === $createdImage->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdImage->retrieve();
    }

    public function testSortedAsc()
    {
        $postfix = $this->randomStr();
        $names = ['b' . $postfix, 'a' . $postfix, 'c' . $postfix];
        $createdImages = [];
        foreach ($names as $name) {
            $image = $this->getService()->createImage([
                'name' => $name,
            ]);

            $createdImages[] = $image;
        }

        $rightOrder = ['a' . $postfix, 'b' . $postfix, 'c' . $postfix];
        $i = 0;
        require_once $this->sampleFile(
            'images/list_sorted.php',
            [
                '{sortKey}' => 'name',
                '{sortDir}' => 'asc',
                '/** @var \OpenStack\Images\v2\Models\Image $image */' => <<<'PHP'
/** @var \OpenStack\Images\v2\Models\Image $image */
if ($image->name === $rightOrder[$i]) {
    $i++;
}
PHP
                ,
            ]
        );

        $this->assertEquals(3, $i);
        foreach ($createdImages as $image) {
            $image->delete();
        }
    }

    public function testSortedDesc()
    {
        $postfix = $this->randomStr();
        $names = ['b' . $postfix, 'a' . $postfix, 'c' . $postfix];
        $createdImages = [];
        foreach ($names as $name) {
            $image = $this->getService()->createImage([
                'name' => $name,
            ]);

            $createdImages[] = $image;
        }

        $rightOrder = ['c' . $postfix, 'b' . $postfix, 'a' . $postfix];
        $i = 0;
        require_once $this->sampleFile(
            'images/list_sorted.php',
            [
                '{sortKey}' => 'name',
                '{sortDir}' => 'desc',
                '/** @var \OpenStack\Images\v2\Models\Image $image */' => <<<'PHP'
/** @var \OpenStack\Images\v2\Models\Image $image */
if ($image->name === $rightOrder[$i]) {
    $i++;
}
PHP
                ,
            ]
        );

        $this->assertEquals(3, $i);
        foreach ($createdImages as $image) {
            $image->delete();
        }
    }
}