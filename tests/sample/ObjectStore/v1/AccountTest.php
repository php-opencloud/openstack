<?php

namespace OpenStack\Sample\ObjectStore\v1;

class AccountTest extends TestCase
{
    public function testMergeMetadata()
    {
        $fooVal = $this->randomStr();
        $barVal = $this->randomStr();

        require_once $this->sampleFile('account/merge_metadata.php', [
            '{key_1}' => 'Foo',
            '{key_2}' => 'Bar',
            '{val_1}' => $fooVal,
            '{val_2}' => $barVal,
        ]);

        $metadata = $this->getService()->getAccount()->getMetadata();
        $this->assertEquals($fooVal, $metadata['Foo']);
        $this->assertEquals($barVal, $metadata['Bar']);
    }

    /**
     * @depends testMergeMetadata
     */
    public function testGetMetadata()
    {
        /** @var array $metadata */
        require_once $this->sampleFile('account/get_metadata.php');

        $this->assertArrayHasKey('Foo', $metadata);
        $this->assertArrayHasKey('Bar', $metadata);
    }

    /**
     * @depends testMergeMetadata
     */
    public function testResetMetadata()
    {
        $fooVal = $this->randomStr();
        $barVal = $this->randomStr();

        require_once $this->sampleFile('account/reset_metadata.php', [
            '{key_1}' => 'Foo1',
            '{key_2}' => 'Bar1',
            '{val_1}' => $fooVal,
            '{val_2}' => $barVal,
        ]);

        $metadata = $this->getService()->getAccount()->getMetadata();
        $this->assertEquals($fooVal, $metadata['Foo1']);
        $this->assertEquals($barVal, $metadata['Bar1']);
        $this->assertArrayNotHasKey('Foo', $metadata);
        $this->assertArrayNotHasKey('Bar', $metadata);
    }
}