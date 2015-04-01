<?php

namespace spec\OpenStack\Common\Api;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HeaderSerializerSpec extends ObjectBehavior
{
    function it_stocks_headers_of_request()
    {
        $definition = include 'fixtures/headers.php';

        $userValues = [
            'name'     => 'john_doe',
            'age'      => 30,
            'metadata' => ['hair_color' => 'brown'],
            'other'    => 'blah'
        ];

        $expectedHeaders = [
            'X-Foo-Name'        => $userValues['name'],
            'age'               => $userValues['age'],
            'X-Meta-hair_color' => $userValues['metadata']['hair_color'],
        ];

        $this->serialize($userValues, $definition)->shouldReturn($expectedHeaders);
    }
}
