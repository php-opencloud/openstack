<?php

namespace OpenStack\Test\Common\Service;

use OpenStack\Common\Service\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    function setUp()
    {
        $this->builder = new Builder([]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_username_is_missing()
    {
        $this->builder->createService('Compute', 2, []);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_password_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_both_tenantId_and_tenantName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6, 'catalogType' => 7,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_authUrl_is_missing()
    {
        $this->builder->createService('Compute', 2, ['username' => 1, 'password' => 2, 'tenantId' => 3]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_region_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_catalogName_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4,
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_it_throws_exception_if_catalogType_is_missing()
    {
        $this->builder->createService('Compute', 2, [
            'username' => 1, 'password' => 2, 'tenantId' => 3, 'authUrl' => 4, 'region' => 5, 'catalogName' => 6,
        ]);
    }
}
