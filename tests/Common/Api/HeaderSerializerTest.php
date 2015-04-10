<?php

namespace OpenStack\Test\Common\Api;

use OpenStack\Common\Api\HeaderSerializer;
use OpenStack\Common\Api\Operation;

class HeaderSerializerSpec extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    public function setUp()
    {
        $this->serializer = new HeaderSerializer();
    }

    public function testHeadersOfRequestAreStocked()
    {
        $definition = include 'fixtures/headers.php';

        $userValues = [
            'name'     => 'john_doe',
            'age'      => 30,
            'metadata' => ['hair_color' => 'brown'],
            'other'    => 'blah'
        ];

        $expected = [
            'X-Foo-Name'        => $userValues['name'],
            'age'               => $userValues['age'],
            'X-Meta-hair_color' => $userValues['metadata']['hair_color'],
        ];

        $actual = $this->serializer->serialize($userValues, Operation::toParamArray($definition['params']));

        $this->assertEquals($expected, $actual);
    }
}
