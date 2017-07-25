<?php

namespace OpenStack\Test\Metric\v1\Gnocchi\Models;

use OpenStack\Metric\v1\Gnocchi\Models\Metric;
use OpenStack\Metric\v1\Gnocchi\Models\Resource;
use OpenStack\Metric\v1\Gnocchi\Api;
use OpenStack\Test\TestCase;

class ResourceTest extends TestCase
{
    /** @var Resource */
    private $resource;

    public function setUp()
    {
        parent::setUp();

        $this->rootFixturesDir = dirname(__DIR__);

        $this->resource = new Resource($this->client->reveal(), new Api());
        $this->resource->type = 'generic';
        $this->resource->id = '1111';
    }

    public function test_it_retrieves()
    {
        $this->setupMock('GET', 'v1/resource/generic/1111', null, [], 'resource-get');
        $this->resource->retrieve();
        $this->assertEquals('fake-project-id', $this->resource->projectId);
        $this->assertEquals('fake-created-by-user-id', $this->resource->createdByUserId);
        $this->assertEquals('fake-type', $this->resource->type);
        $this->assertInternalType('array', $this->resource->metrics);
        $this->assertEquals(8, count($this->resource->metrics));
    }


    public function test_it_gets_metric()
    {
        $this->setupMock('GET', sprintf('v1/resource/generic/1111/metric/storage.objects.outgoing.bytes'), [], [], 'metric-get');

        /** @var Metric $metric */
        $metric = $this->resource->getMetric('storage.objects.outgoing.bytes');

        $this->assertInstanceOf(Metric::class, $metric);
        $this->assertEquals($metric->name, 'storage.objects.outgoing.bytes');
        $this->assertEquals($metric->id, '000b7bf8-0271-46dd-90aa-cfe89026a55a');
    }

    public function test_it_gets_metric_measures()
    {
        $this->setupMock('GET', sprintf('v1/resource/generic/1111/metric/storage.objects.outgoing.bytes/measures'), [], [], 'resource-metric-measures-get');
        $measures = $this->resource->getMetricMeasures(['metric' => 'storage.objects.outgoing.bytes']);

        $this->assertInternalType('array', $measures);
        $this->assertEquals(7, count($measures));
        $this->assertEquals('2017-05-16T00:00:00+00:00', $measures[0][0]);
    }

    public function test_it_lists_resource_metrics()
    {
        $this->setupMock('GET', 'v1/resource/generic/1111/metric', [], [], 'resource-metrics-get');

        $result = iterator_to_array($this->resource->listResourceMetrics());

        $this->assertEquals(23, count($result));
        $this->assertContainsOnlyInstancesOf(Metric::class, $result);
    }
}
