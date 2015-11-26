<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Transport\Utils;

class Iterator
{
    private $requestFn;
    private $resourceFn;
    private $limit;
    private $count;
    private $resourcesKey;
    private $markerKey;
    private $mapFn;
    private $currentMarker;

    public function __construct(array $options, callable $requestFn, callable $resourceFn)
    {
        $this->limit = isset($options['limit']) ? $options['limit'] : false;
        $this->count = 0;

        if (isset($options['resourcesKey'])) {
            $this->resourcesKey = $options['resourcesKey'];
        }

        if (isset($options['markerKey'])) {
            $this->markerKey = $options['markerKey'];
        }

        if (isset($options['mapFn']) && is_callable($options['mapFn'])) {
            $this->mapFn = $options['mapFn'];
        }

        $this->requestFn  = $requestFn;
        $this->resourceFn = $resourceFn;
    }

    private function totalReached()
    {
        return $this->limit && $this->count >= $this->limit;
    }

    private function fetchResources()
    {
        $response = call_user_func($this->requestFn, $this->currentMarker);

        $json = Utils::flattenJson(Utils::jsonDecode($response), $this->resourcesKey);

        if ($response->getStatusCode() === 204 || empty($json)) {
            return false;
        }

        return $json;
    }

    private function assembleResource(array $data)
    {
        $resource = call_user_func($this->resourceFn, $data);

        // Invoke user-provided fn if provided
        if ($this->mapFn) {
            call_user_func_array($this->mapFn, [&$resource]);
        }

        // Update marker if operation supports it
        if ($this->markerKey) {
            $this->currentMarker = $resource->{$this->markerKey};
        }

        return $resource;
    }

    public function __invoke()
    {
        while (true) {
            // Fetch new collection from API. Break loop if empty set returned
            if (false === ($resources = $this->fetchResources())) {
                break;
            }

            foreach ($resources as $resourceData) {
                // Halt if user-provided limit is reached
                if ($this->totalReached()) {
                    break;
                }

                $this->count++;

                yield $this->assembleResource($resourceData);
            }

            // If user-provided limit has been reached, or if the operation does not support pagination, halt the
            // loop without sending another request.
            if ($this->totalReached() || !$this->markerKey) {
                break;
            }
        }
    }
}