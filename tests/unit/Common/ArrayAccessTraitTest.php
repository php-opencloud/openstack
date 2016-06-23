<?php

namespace OpenStack\Test\Common;

use OpenStack\Common\ArrayAccessTrait;
use OpenStack\Test\TestCase;

class ArrayAccessTraitTest extends TestCase
{
    private $aa;

    public function setUp()
    {
        $this->aa = new ArrayAccess();
    }

    public function test_offset_is_set()
    {
        $this->aa->offsetSet('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->aa->getElements());
    }

    public function test_it_appends_if_no_key_is_set()
    {
        $this->aa->offsetSet(null, 'bar');
        $this->assertEquals(['bar'], $this->aa->getElements());
    }

    public function test_if_checks_if_offset_exists()
    {
        $this->aa->offsetSet('bar', 'foo');
        $this->assertTrue($this->aa->offsetExists('bar'));
        $this->assertFalse($this->aa->offsetExists('baz'));
    }

    public function test_if_gets_offset()
    {
        $this->aa->offsetSet('bar', 'foo');
        $this->assertEquals('foo', $this->aa->offsetGet('bar'));
        $this->assertNull($this->aa->offsetGet('baz'));
    }

    public function test_it_unsets_offset()
    {
        $this->aa->offsetSet('bar', 'foo');
        $this->aa->offsetUnset('bar');
        $this->assertNull($this->aa->offsetGet('bar'));
    }
}

class ArrayAccess
{
    use ArrayAccessTrait;

    public function getElements()
    {
        return $this->internalState;
    }
}
