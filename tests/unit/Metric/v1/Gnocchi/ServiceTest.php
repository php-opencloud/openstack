<?php

namespace OpenStack\Test\Metric\v1\Gnocchi;

use OpenStack\Metric\v1\Gnocchi\Models\Metric;
use OpenStack\Metric\v1\Gnocchi\Models\Resource;
use OpenStack\Metric\v1\Gnocchi\Models\ResourceType;
use OpenStack\Test\TestCase;
use OpenStack\Metric\v1\Gnocchi\Api;
use OpenStack\Metric\v1\Gnocchi\Service;

class ServiceTest extends TestCase
{
    /** @var Service */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->rootFixturesDir = __DIR__;
        $this->service = new Service($this->client->reveal(), new Api());
    }

    public function test_it_lists_resource_types()
    {
        $this->mockRequest('GET', 'v1/resource_type', 'resourcetypes-get');

        $result = iterator_to_array($this->service->listResourceTypes());

        self::assertEquals(15, count($result));
        self::assertContainsOnlyInstancesOf(ResourceType::class, $result);
    }

    public function test_it_lists_resources()
    {
        $this->mockRequest('GET', ['path' => 'v1/resource/generic', 'query' => ['limit' => 3]], 'resources-get');

        $result = iterator_to_array($this->service->listResources(['limit' => 3]));

        self::assertEquals(3, count($result));
        self::assertContainsOnlyInstancesOf(Resource::class, $result);
    }

    public function test_it_get_resource()
    {
        $resource = $this->service->getResource(['id' => '1']);

        self::assertEquals('1', $resource->id);
        self::assertInstanceOf(Resource::class, $resource);
    }

    public function test_it_search_resources()
    {
        $this->mockRequest(
            'POST',
            'v1/search/resource/generic',
            'resources-get',
            [],
            ['Content-Type' => 'application/json']
        );

        $result = $this->service->searchResources(['type' => 'generic']);
        self::assertContainsOnlyInstancesOf(Resource::class, $result);
    }

    public function test_it_search_resources_with_custom_type()
    {
        $this->mockRequest(
            'POST',
            'v1/search/resource/instance',
            'resources-get',
            [],
            ['Content-Type' => 'application/json']
        );

        $result = $this->service->searchResources(['type' => 'instance']);

        self::assertContainsOnlyInstancesOf(Resource::class, $result);
    }

    public function test_it_lists_metrics()
    {
        $this->mockRequest('GET', ['path' => 'v1/metric', 'query' => ['limit' => 5]], 'metrics-get');

        $result = $this->service->listMetrics(['limit' => 5]);

        self::assertContainsOnlyInstancesOf(Metric::class, $result);
    }

    public function test_it_get_metric()
    {
        self::assertInstanceOf(Metric::class, $this->service->getMetric('metric-id'));
    }
}
