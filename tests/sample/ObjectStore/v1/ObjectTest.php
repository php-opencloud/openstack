<?php

namespace OpenStack\Sample\ObjectStore\v1;

use OpenStack\ObjectStore\v1\Models\StorageObject;
use Psr\Http\Message\StreamInterface;

class ObjectTest extends TestCase
{
    public function testCreate(): StorageObject
    {
        $containerName = $this->randomStr();
        $objectName = $this->randomStr();

        $this->getService()->createContainer(['name' => $containerName]);

        /** @var StorageObject $object */
        require_once $this->sampleFile('objects/create.php', [
            '{containerName}' => $containerName,
            '{objectName}' => $objectName,
            '{objectContent}' => str_repeat('A', 1000),
        ]);

        $this->assertInstanceOf(StorageObject::class, $object);

        return $object;
    }

    /**
     * @depends testCreate
     */
    public function testCheckExists(StorageObject $createdObject)
    {
        /** @var bool $exists */
        require_once $this->sampleFile('objects/check_exists.php', [
            '{containerName}' => $createdObject->containerName,
            '{objectName}' => $createdObject->name,
        ]);

        $this->assertTrue($exists);
    }

    /**
     * @depends testCreate
     */
    public function testCopy(StorageObject $createdObject)
    {
        $newName = $this->randomStr();
        $container = $this->getService()->getContainer($createdObject->containerName);

        $this->assertFalse($container->objectExists($newName));

        require_once $this->sampleFile('objects/copy.php', [
            '{containerName}' => $container->name,
            '{objectName}' => $createdObject->name,
            '{newContainerName}' => $container->name,
            '{newObjectName}' => $newName,
        ]);

        $this->assertTrue($container->objectExists($newName));
        $container->getObject($newName)->delete();
    }

    /**
     * @depends testCreate
     */
    public function testDownload(StorageObject $createdObject)
    {
        /** @var StreamInterface $stream */
        require_once $this->sampleFile('objects/download.php', [
            '{containerName}' => $createdObject->containerName,
            '{objectName}' => $createdObject->name,
        ]);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals(1000, $stream->getSize());
        $this->assertEquals(str_repeat('A', 1000), $stream->getContents());
    }

    /**
     * @depends testCreate
     */
    public function testDownloadStream(StorageObject $createdObject)
    {
        /** @var StreamInterface $stream */
        require_once $this->sampleFile('objects/download_stream.php', [
            '{containerName}' => $createdObject->containerName,
            '{objectName}' => $createdObject->name,
        ]);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertNull($stream->getSize());

        $body = '';
        while (!$stream->eof()) {
            $body .= $stream->read(64);
        }
        $this->assertEquals(str_repeat('A', 1000), $body);
    }

    /**
     * @depends testCreate
     */
    public function testRead(StorageObject $createdObject)
    {
        $createdObject->retrieve();

        /** @var StorageObject $object */
        require_once $this->sampleFile('objects/read.php', [
            '{containerName}' => $createdObject->containerName,
            '{objectName}' => $createdObject->name,
        ]);

        $this->assertInstanceOf(StorageObject::class, $object);
        $this->assertEquals($createdObject->name, $object->name);
        $this->assertEquals($createdObject->containerName, $object->containerName);
        $this->assertEquals($createdObject->contentLength, $object->contentLength);
        $this->assertEquals($createdObject->hash, $object->hash);
        $this->assertEquals([], $object->metadata);
    }

    /**
     * @depends testCreate
     */
    public function testList(StorageObject $createdObject)
    {
        $found = false;
        require_once $this->sampleFile('objects/list.php', [
            '/** @var \OpenStack\ObjectStore\v1\Models\StorageObject $object */' => <<<'PHP'
/** @var \OpenStack\ObjectStore\v1\Models\StorageObject $object */
if ($object->name === $createdObject->name) {
    $found = true;
}
PHP
            ,
            '{containerName}' => $createdObject->containerName,
        ]);

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     */
    public function testMergeMetadata(StorageObject $createdObject)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();
        $barVal = $this->randomStr();

        $createdObject->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'objects/merge_metadata.php',
            [
                '{containerName}' => $createdObject->containerName,
                '{objectName}' => $createdObject->name,
                '{key_1}' => 'Foo',
                '{val_1}' => $fooVal,
                '{key_2}' => 'Bar',
                '{val_2}' => $barVal,
            ]
        );

        $metadata = $createdObject->getMetadata();
        $this->assertEquals($initVal, $metadata['Init']);
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertEquals($barVal, $metadata['Bar']);
    }

    /**
     * @depends testCreate
     * @depends testMergeMetadata
     */
    public function testGetMetadata(StorageObject $createdObject)
    {
        /** @var array $metadata */
        require_once $this->sampleFile('objects/get_metadata.php', [
            '{containerName}' => $createdObject->containerName,
            '{objectName}' => $createdObject->name,
        ]);

        $createdObject->retrieve();

        $this->assertEquals($createdObject->metadata['Init'], $metadata['Init']);
        $this->assertEquals($createdObject->metadata['Foo'], $metadata['Foo']);
        $this->assertEquals($createdObject->metadata['Bar'], $metadata['Bar']);
    }

    /**
     * @depends testCreate
     */
    public function testResetMetadata(StorageObject $createdObject)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();
        $barVal = $this->randomStr();

        $createdObject->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile(
            'objects/reset_metadata.php',
            [
                '{containerName}' => $createdObject->containerName,
                '{objectName}' => $createdObject->name,
                '{key_1}' => 'Foo',
                '{val_1}' => $fooVal,
                '{key_2}' => 'Bar',
                '{val_2}' => $barVal,
            ]
        );

        $metadata = $createdObject->getMetadata();
        $this->assertArrayNotHasKey('Init', $metadata);
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertEquals($barVal, $metadata['Bar']);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(StorageObject $createdObject)
    {
        $container = $this->getService()->getContainer($createdObject->containerName);

        require_once $this->sampleFile('objects/delete.php', [
            '{containerName}' => $createdObject->containerName,
            '{objectName}' => $createdObject->name,
        ]);

        $this->assertFalse($container->objectExists($createdObject->name));
        $container->delete();
    }
}