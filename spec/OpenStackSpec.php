<?php

namespace spec\OpenStack;

use OpenStack\Common\Service\Builder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OpenStackSpec extends ObjectBehavior
{
    private $builder;

    function let(Builder $builder)
    {
        $this->builder = $builder;

        $this->beConstructedWith([], $this->builder);
    }

    function it_supports_object_store_v2()
    {
        $this->builder->createService('ObjectStore', 2, ['catalogName' => 'swift', 'catalogType' => 'object-store'])
            ->shouldBeCalled();

        $this->objectStoreV2();
    }

    function it_supports_compute_v2()
    {
        $this->builder->createService('Compute', 2, ['catalogName' => 'nova', 'catalogType' => 'compute'])
            ->shouldBeCalled();

        $this->computeV2();
    }
}