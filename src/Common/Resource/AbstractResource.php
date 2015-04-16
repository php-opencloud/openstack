<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Operator;
use GuzzleHttp\Message\ResponseInterface;

abstract class AbstractResource extends Operator implements ResourceInterface
{
    const DEFAULT_MARKER_KEY = 'id';

    protected $resourceKey;
    protected $resourcesKey;
    protected $markerKey;
    protected $aliases = [];

    /**
     * @codeCoverageIgnore
     */
    protected function getServiceNamespace()
    {
        return str_replace('\\Models', '', $this->getCurrentNamespace());
    }

    private function flatten(array $data, $key = null)
    {
        $key = $key ?: $this->resourceKey;
        return $key && isset($data[$key]) ? $data[$key] : $data;
    }

    public function populateFromResponse(ResponseInterface $response)
    {
        $json = $response->json();

        if (!empty($json)) {
            $this->populateFromArray($this->flatten($json));
        }

        return $this;
    }

    public function populateFromArray(array $array)
    {
        foreach ($array as $key => $val) {
            $property = isset($this->aliases[$key]) ? $this->aliases[$key] : $key;
            if (property_exists($this, $property)) {
                $this->$property = $val;
            }
        }
    }

    protected function getAttrs(array $keys)
    {
        $output = [];

        foreach ($keys as $key) {
            if (property_exists($this, $key)) {
                //$aliases = array_flip($this->aliases);
                //$alias = isset($aliases[$key]) ? $aliases[$key] : $key;
                $output[$key] = $this->$key;
            }
        }

        return $output;
    }

    public function enumerate(Operation $operation, callable $mapFn = null)
    {
        $limit = $operation->getValue('limit') ?: false;
        $supportsPagination = $operation->hasParam('marker');
        $markerKey = $this->markerKey ?: self::DEFAULT_MARKER_KEY;

        $count = 0;
        $moreRequestsRequired = true;
        $totalReached = false;

        while ($moreRequestsRequired) {

            $response = $operation->send();
            $body = $response->json();
            $json = $this->flatten($body, $this->resourcesKey);

            foreach ($json as $resourceData) {
                if ($limit && $count >= $limit) {
                    $totalReached = true;
                    break;
                }

                $count++;

                $resource = $this->newInstance();
                $resource->populateFromArray($resourceData);

                if ($mapFn) {
                    call_user_func_array($mapFn, [$resource]);
                }

                if ($supportsPagination) {
                    $operation->setValue('marker', $resource->$markerKey);
                }

                yield $resource;
            }

            if ($totalReached || !$supportsPagination || empty($json)) {
                $moreRequestsRequired = false;
            }
        }
    }
}