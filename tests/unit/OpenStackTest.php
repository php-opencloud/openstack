<?php

namespace OpenStack\Test;

use OpenStack\Common\Service\Builder;
use OpenStack\OpenStack;
use Prophecy\PhpUnit\ProphecyTestCase;

class OpenStackTest extends ProphecyTestCase
{
    private $builder;
    private $openstack;

    function setUp()
    {
        $this->builder = $this->prophesize(Builder::class);
        $this->openstack = new OpenStack([], $this->builder->reveal());
    }

    public function test_it_supports_compute_v2()
    {
        $this->builder
            ->createService('Compute', 2, ['catalogName' => 'nova', 'catalogType' => 'compute'])
            ->shouldBeCalled();

        $this->openstack->computeV2();
    }

    public function test_it_supports_identity_v2()
    {
        $this->builder
            ->createService('Identity', 2, ['catalogName' => false, 'catalogType' => false])
            ->shouldBeCalled();

        $this->openstack->identityV2();
    }

    public function test_it_supports_identity_v3()
    {
        $this->builder
            ->createService('Identity', 3, ['catalogName' => false, 'catalogType' => false])
            ->shouldBeCalled();

        $this->openstack->identityV3();
    }
}
