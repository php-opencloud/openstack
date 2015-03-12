<?php

namespace spec\OpenStack;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OpenStackSpec extends ObjectBehavior
{
    private $options;

    function let()
    {
        $this->options = ['authUrl' => '1', 'username' => '2', 'password' => '3', 'tenantId' => '4'];

        $this->beConstructedWith($this->options);
    }

    function it_supports_object_store_v2()
    {
        $this->getObjectStoreV2()->shouldReturnAnInstanceOf('OpenStack\ObjectStore\v2\Service');
    }

    function it_supports_compute_v2()
    {
        $this->getComputeV2()->shouldReturnAnInstanceOf('OpenStack\Compute\v2\Service');
    }
}
