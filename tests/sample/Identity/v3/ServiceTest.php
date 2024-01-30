<?php

namespace OpenStack\Sample\Identity\v3;

use OpenStack\Common\Error\BadResponseError;
use OpenStack\Identity\v3\Service;

class ServiceTest extends TestCase
{
    public function testCreate(): \OpenStack\Identity\v3\Models\Service
    {
        /** @var $service \OpenStack\Identity\v3\Models\Service */
        require_once $this->sampleFile(
            'services/create.php',
            [
                '{serviceName}' => $this->randomStr(),
                '{serviceType}' => $this->randomStr(),
            ]
        );
        self::assertInstanceOf(\OpenStack\Identity\v3\Models\Service::class, $service);

        return $service;
    }

    /**
     * @depends testCreate
     */
    public function testList(\OpenStack\Identity\v3\Models\Service $createdService)
    {
        $found = false;
        require_once $this->sampleFile(
            'services/list.php',
            [
                '/** @var $service \OpenStack\Identity\v3\Models\Service */' => <<<'PHP'
/** @var $service \OpenStack\Identity\v3\Models\Service */
if ($service->id === $createdService->id) {
    $found = true;
}
PHP
                ,
            ]
        );

        $this->assertTrue($found);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(\OpenStack\Identity\v3\Models\Service $createdService)
    {
        $newDescription = $this->randomStr();

        require_once $this->sampleFile(
            'services/update.php',
            [
                '{serviceId}' => $createdService->id,
                '{description}' => $newDescription,
            ]
        );

        $createdService->retrieve();
        $this->assertEquals($newDescription, $createdService->description);
    }

    /**
     * @depends testCreate
     */
    public function testGet(\OpenStack\Identity\v3\Models\Service $createdService)
    {
        /** @var $service \OpenStack\Identity\v3\Models\Service */
        require_once $this->sampleFile(
            'services/read.php',
            [
                '{serviceId}' => $createdService->id,
            ]
        );

        $this->assertEquals($createdService->id, $service->id);
        $this->assertEquals($createdService->name, $service->name);
        $this->assertEquals($createdService->description, $service->description);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(\OpenStack\Identity\v3\Models\Service $createdService)
    {
        require_once $this->sampleFile(
            'services/delete.php',
            [
                '{serviceId}' => $createdService->id,
            ]
        );

        $found = false;
        foreach ($this->getService()->listServices() as $service) {
            if ($service->id === $createdService->id) {
                $found = true;
            }
        }

        $this->assertFalse($found);

        $this->expectException(BadResponseError::class);
        $createdService->retrieve();
    }
}