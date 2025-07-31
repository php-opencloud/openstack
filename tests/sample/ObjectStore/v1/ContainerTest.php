<?php

namespace OpenStack\Sample\ObjectStore\v1;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\ObjectStore\v1\Models\Container;

class ContainerTest extends TestCase
{
    public function testCreate(): Container
    {
        $containerName = $this->randomStr();

        /** @var \OpenStack\ObjectStore\v1\Models\Container $container */
        require_once $this->sampleFile('containers/create.php', ['{containerName}' => $containerName]);

        $this->assertInstanceOf(Container::class, $container);

        return $container;
    }

    /**
     * @depends testCreate
     */
    public function testRead(Container $createdContainer)
    {
        /** @var \OpenStack\ObjectStore\v1\Models\Container $container */
        require_once $this->sampleFile('containers/read.php', ['{containerName}' => $createdContainer->name]);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertEquals($createdContainer->name, $container->name);
        $this->assertEquals(0, $container->objectCount);
        $this->assertEquals([], $container->metadata);
    }

    /**
     * @depends testCreate
     */
    public function testList(Container $createdContainer)
    {
        $found = false;
        require_once $this->sampleFile('containers/list.php', [
            '/** @var \OpenStack\ObjectStore\v1\Models\Container $container */' => <<<'PHP'
/** @var \OpenStack\ObjectStore\v1\Models\Container $container */
if ($container->name === $createdContainer->name) {
    $found = true;
}
PHP
            ,
        ]);

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     */
    public function testMergeMetadata(Container $createdContainer)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();
        $barVal = $this->randomStr();

        $createdContainer->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile('containers/merge_metadata.php', [
            '{key_1}' => 'Foo',
            '{key_2}' => 'Bar',
            '{val_1}' => $fooVal,
            '{val_2}' => $barVal,
            '{containerName}' => $createdContainer->name,
        ]);

        $metadata = $createdContainer->getMetadata();
        $this->assertEquals($initVal, $metadata['Init']);
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertEquals($barVal, $metadata['Bar']);
    }

    /**
     * @depends testCreate
     * @depends testMergeMetadata
     */
    public function testGetMetadata(Container $createdContainer)
    {
        /** @var array $metadata */
        require_once $this->sampleFile('containers/get_metadata.php', ['{containerName}' => $createdContainer->name]);

        $this->assertArrayHasKey('Init', $metadata);
        $this->assertArrayHasKey('Foo', $metadata);
        $this->assertArrayHasKey('Bar', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testResetMetadata(Container $createdContainer)
    {
        $initVal = $this->randomStr();
        $fooVal = $this->randomStr();
        $barVal = $this->randomStr();

        $createdContainer->mergeMetadata(['Init' => $initVal]);

        require_once $this->sampleFile('containers/reset_metadata.php', [
            '{key_1}' => 'Foo',
            '{key_2}' => 'Bar',
            '{val_1}' => $fooVal,
            '{val_2}' => $barVal,
            '{containerName}' => $createdContainer->name,
        ]);

        $metadata = $createdContainer->getMetadata();
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertEquals($barVal, $metadata['Bar']);
        $this->assertArrayNotHasKey('Init', $metadata);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(Container $createdContainer)
    {
        require_once $this->sampleFile('containers/delete.php', ['{containerName}' => $createdContainer->name]);

        foreach ($this->getService()->listContainers() as $container) {
            if ($container->name === $createdContainer->name) {
                $this->fail('Container still exists');
            }
        }

        $this->expectException(BadResponseError::class);
        $createdContainer->retrieve();
    }

    public function testTokensContainer()
    {
        $container = $this->getService()->createContainer(['name' => $this->randomStr() . '_tokens']);
        $this->assertNotNull($container->name);

        // this would send POST request with 'tokens' in the URL
        $container->resetMetadata([]);
    }
}