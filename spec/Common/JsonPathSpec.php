<?php

namespace spec\OpenStack\Common;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JsonPathSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_sets_values_according_to_paths()
    {
        $this->set('foo.bar.baz', 'VALUE');
        $this->getStructure()->shouldReturn([
            'foo' => [
                'bar' => [
                    'baz' => 'VALUE',
                ]
            ]
        ]);
    }

    function it_sets_arrays_according_to_paths()
    {
        $this->beConstructedWith([
            'foo' => [
                'bar' => [
                    'value' => 'VALUE',
                ]
            ]
        ]);

        $this->set('foo.bar.items', ['item_1', 'item_2']);
        $this->getStructure()->shouldReturn([
            'foo' => [
                'bar' => [
                    'value' => 'VALUE',
                    'items' => ['item_1', 'item_2'],
                ]
            ]
        ]);
    }

    function it_gets_values_according_to_paths()
    {
        $this->beConstructedWith([
            'foo' => [
                'bar' => [
                    'baz' => 'VALUE_1',
                    'lol' => 'VALUE_2',
                ]
            ]
        ]);

        $this->get('foo.bar.baz')->shouldReturn('VALUE_1');
        $this->get('foo.bar.lol')->shouldReturn('VALUE_2');
        $this->get('foo.bar.boo')->shouldReturn(null);
    }
}