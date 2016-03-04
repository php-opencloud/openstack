<?php

namespace OpenStack\Test;

use OpenStack\Common\Service\Builder;
use OpenStack\OpenStack;

class OpenStackTest extends TestCase
{
    private $builder;
    private $openstack;

    public function setUp()
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
    
    public function test_it_supports_networking_v2()
    {
        $this->builder
            ->createService('Networking', 2, ['catalogName' => 'neutron', 'catalogType' => 'network'])
            ->shouldBeCalled();

        $this->openstack->networkingV2();
    }

    public function test_it_supports_object_store_v1()
    {
        $this->builder
            ->createService('ObjectStore', 1, ['catalogName' => 'swift', 'catalogType' => 'object-store'])
            ->shouldBeCalled();

        $this->openstack->objectStoreV1();
    }

    public function test_it_supports_block_storage_v2()
    {
        $this->builder
            ->createService('BlockStorage', 2, ['catalogName' => 'cinderv2', 'catalogType' => 'volumev2'])
            ->shouldBeCalled();

        $this->openstack->blockStorageV2();
    }

    public function test_it_supports_images_v2()
    {
        $this->builder
            ->createService('Images', 2, ['catalogName' => 'glance', 'catalogType' => 'image'])
            ->shouldBeCalled();

        $this->openstack->imagesV2();
    }
}
