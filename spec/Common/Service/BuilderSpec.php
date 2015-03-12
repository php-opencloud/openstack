<?php

namespace spec\OpenStack\Common\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BuilderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_throws_exception_if_username_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, []);
    }

    function it_throws_exception_if_password_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, ['username' => 1]);
    }

    function it_throws_exception_if_both_tenantId_and_tenantName_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, [
            'username' => 1, 'password' => 2, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6, 'catalogType' => 7,
        ]);
    }

    function it_throws_exception_if_authUrl_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, ['username' => 1, 'password' => 2, 'tenantId' => 3]);
    }

    function it_throws_exception_if_region_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    function it_throws_exception_if_catalogName_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    function it_throws_exception_if_catalogType_is_missing()
    {
        $this->shouldThrow('\Exception')->duringCreateService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6,
        ]);
    }
}
