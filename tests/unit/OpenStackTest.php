<?php

namespace OpenStack\Test;

use GuzzleHttp\ClientInterface;
use OpenCloud\Test\TestCase;
use OpenCloud\Common\Service\Builder;
use OpenStack\Identity\v3\Api;
use OpenStack\OpenStack;

class OpenStackTest extends TestCase
{
    private $builder;
    private $openstack;

    public function setUp()
    {
        $this->builder = $this->prophesize(Builder::class);
        $this->openstack = new OpenStack(['authUrl' => ''], $this->builder->reveal());
    }

    public function test_it_supports_compute_v2()
    {
        $this->builder
            ->createService('Compute', 2, ['catalogName' => 'nova', 'catalogType' => 'compute'])
            ->shouldBeCalled()
            ->willReturn($this->service('Compute', 2));

        $this->openstack->computeV2();
    }

    public function test_it_supports_identity_v2()
    {
        $this->builder
            ->createService('Identity', 2, ['catalogName' => false, 'catalogType' => false])
            ->shouldBeCalled()
            ->willReturn($this->service('Identity', 2));

        $this->openstack->identityV2();
    }

    public function test_it_supports_identity_v3()
    {
        $this->builder
            ->createService('Identity', 3, ['catalogName' => false, 'catalogType' => false])
            ->shouldBeCalled()
            ->willReturn($this->service('Identity', 3));

        $this->openstack->identityV3();
    }
    
    public function test_it_supports_networking_v2()
    {
        $this->builder
            ->createService('Networking', 2, ['catalogName' => 'neutron', 'catalogType' => 'network'])
            ->shouldBeCalled()
            ->willReturn($this->service('Networking', 2));

        $this->openstack->networkingV2();
    }

    public function test_it_supports_object_store_v1()
    {
        $this->builder
            ->createService('ObjectStore', 1, ['catalogName' => 'swift', 'catalogType' => 'object-store'])
            ->shouldBeCalled()
            ->willReturn($this->service('ObjectStore', 1));

        $this->openstack->objectStoreV1();
    }

    public function test_it_supports_block_storage_v2()
    {
        $this->builder
            ->createService('BlockStorage', 2, ['catalogName' => 'cinderv2', 'catalogType' => 'volumev2'])
            ->shouldBeCalled()
            ->willReturn($this->service('BlockStorage', 2));

        $this->openstack->blockStorageV2();
    }

    public function test_it_supports_images_v2()
    {
        $this->builder
            ->createService('Images', 2, ['catalogName' => 'glance', 'catalogType' => 'image'])
            ->shouldBeCalled()
            ->willReturn($this->service('Images', 2));

        $this->openstack->imagesV2();
    }

    private function service($service, $version)
    {
        $class = sprintf("OpenStack\\%s\\v%d\\Service", $service, $version);

        return new $class($this->prophesize(ClientInterface::class)->reveal(), new Api());
    }
}
