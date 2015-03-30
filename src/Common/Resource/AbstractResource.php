<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operator;
use GuzzleHttp\Message\ResponseInterface;

abstract class AbstractResource extends Operator implements ResourceInterface
{
    protected $aliases = [];

    protected function getServiceNamespace()
    {
        return str_replace('\\Models', '', $this->getCurrentNamespace());
    }

    public function populateFromResponse(ResponseInterface $response, array $definition = [])
    {
        $json = $response->json();

        if (isset($definition['responseKey']) && isset($json[$definition['responseKey']])) {
            $json = $json[$definition['responseKey']];
        }

        $this->populateFromArray($json);

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