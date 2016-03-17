<?php

namespace OpenCloud\Test\Common\Transport;

use OpenCloud\Common\Api\Parameter;
use OpenCloud\Common\Transport\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    public function setUp()
    {
        $this->serializer = new JsonSerializer();
    }

    public function test_it_embeds_params_according_to_path()
    {
        $param = $this->prophesize(Parameter::class);
        $param->isArray()->shouldBeCalled()->willReturn(false);
        $param->isObject()->shouldBeCalled()->willReturn(false);
        $param->getName()->shouldBeCalled()->willReturn('username');
        $param->getPath()->shouldBeCalled()->willReturn('auth.passwordCredentials');

        $userValue = 'fooBar';

        $expected = [
            'auth' => [
                'passwordCredentials' => [
                    'username' => $userValue,
                ],
            ],
        ];

        $actual = $this->serializer->stockJson($param->reveal(), $userValue, []);

        $this->assertEquals($expected, $actual);
    }

    public function test_it_serializes_arrays()
    {
        $param = $this->prophesize(Parameter::class);
        $param->isArray()->shouldBeCalled()->willReturn(true);
        $param->getName()->shouldBeCalled()->willReturn('fooBar');
        $param->getPath()->shouldBeCalled()->willReturn(false);

        $itemSchema = $this->prophesize(Parameter::class);
        $itemSchema->isArray()->shouldBeCalled()->willReturn(false);
        $itemSchema->isObject()->shouldBeCalled()->willReturn(false);
        $itemSchema->getName()->shouldBeCalled()->willReturn('');
        $itemSchema->getPath()->shouldBeCalled()->willReturn('');

        $param->getItemSchema()->shouldBeCalled()->willReturn($itemSchema);

        $userValues = ['1', '2', '3'];

        $expected = ['fooBar' => $userValues];

        $actual = $this->serializer->stockJson($param->reveal(), $userValues, []);

        $this->assertEquals($expected, $actual);
    }

    public function test_it_serializes_objects()
    {
        $prop = $this->prophesize(Parameter::class);
        $prop->isArray()->shouldBeCalled()->willReturn(false);
        $prop->isObject()->shouldBeCalled()->willReturn(false);
        $prop->getName()->shouldBeCalled()->willReturn('foo');
        $prop->getPath()->shouldBeCalled()->willReturn('');

        $param = $this->prophesize(Parameter::class);
        $param->isArray()->shouldBeCalled()->willReturn(false);
        $param->isObject()->shouldBeCalled()->willReturn(true);
        $param->getName()->shouldBeCalled()->willReturn('topLevel');
        $param->getPath()->shouldBeCalled()->willReturn(false);
        $param->getProperty('foo')->shouldBeCalled()->willReturn($prop);

        $expected = ['topLevel' => ['foo' => true]];

        $json = $this->serializer->stockJson($param->reveal(), (object) ['foo' => true], []);

        $this->assertEquals($expected, $json);
    }
}
