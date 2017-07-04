<?php declare(strict_types=1);

namespace OpenStack\Metric\v1\Gnocchi;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Metric\v1\Gnocchi\Models\Metric;
use OpenStack\Metric\v1\Gnocchi\Models\Resource;
use OpenStack\Metric\v1\Gnocchi\Models\ResourceType;

/**
 * Gnocci Metric v1 Service class
 *
 * @property Api $api
 *
 * @package OpenStack\Metric\v1\Gnocchi
 */
class Service extends AbstractService
{
    public function listResourceTypes(): \Generator
    {
        return $this->model(ResourceType::class)->enumerate($this->api->getResourceTypes(), []);
    }

    public function listResources(array $options = []): \Generator
    {
        $this->injectGenericType($options);

        return $this->model(Resource::class)->enumerate($this->api->getResources(), $options);
    }

    public function getResource(array $options = []): Resource
    {
        $this->injectGenericType($options);

        $resource = $this->model(Resource::class);
        $resource->populateFromArray($options);

        return $resource;
    }

    public function searchResources(array $options = []): \Generator
    {
        /**
         * $options['criteria'] must send as STRING
         * This will check input $options and perform json_encode if needed.
         */
        if (isset($options['criteria']) && !is_string($options['criteria'])) {
            $options['criteria'] = json_encode($options['criteria']);
        }

        /**
         * We need to manually add content-type header to this request
         * since searchResources method sends RAW request body.
         */
        $options['contentType'] = 'application/json';

        return $this->model(Resource::class)->enumerate($this->api->searchResources(), $options);
    }

    public function getMetric($id): Metric
    {
        $metric = $this->model(Metric::class);
        $metric->populateFromArray(['id' => $id]);

        return $metric;
    }

    public function listMetrics(array $options = []): \Generator
    {
        return $this->model(Metric::class)->enumerate($this->api->getMetrics(), $options);
    }

    private function injectGenericType(array &$options)
    {
        if (empty($options) || !isset($options['type'])) {
            $options['type'] = Resource::RESOURCE_TYPE_GENERIC;
        }
    }
}
