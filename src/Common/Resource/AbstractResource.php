<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operator;
use GuzzleHttp\Message\ResponseInterface;

abstract class AbstractResource extends Operator implements ResourceInterface
{
    protected $aliases = [];

    /**
     * @codeCoverageIgnore
     */
    protected function getServiceNamespace()
    {
        return str_replace('\\Models', '', $this->getCurrentNamespace());
    }

    public function populateFromResponse(ResponseInterface $response, $resourceKey = null)
    {
        $json = $response->json();

        if (!empty($json)) {
            $json = $resourceKey && isset($json[$resourceKey]) ? $json[$resourceKey] : $json;
            $this->populateFromArray($json);
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
                $output[$key] = $this->$key;
            }
        }

        return $output;
    }
}