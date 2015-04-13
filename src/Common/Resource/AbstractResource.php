<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operator;
use GuzzleHttp\Message\ResponseInterface;

abstract class AbstractResource extends Operator implements ResourceInterface
{
    protected $jsonKey;

    protected $aliases = [];

    /**
     * @codeCoverageIgnore
     */
    protected function getServiceNamespace()
    {
        return str_replace('\\Models', '', $this->getCurrentNamespace());
    }

    public function populateFromResponse(ResponseInterface $response)
    {
        $json = $response->json();

        if (!empty($json)) {
            $json = $this->jsonKey && isset($json[$this->jsonKey]) ? $json[$this->jsonKey] : $json;
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
                //$aliases = array_flip($this->aliases);
                //$alias = isset($aliases[$key]) ? $aliases[$key] : $key;
                $output[$key] = $this->$key;
            }
        }

        return $output;
    }
}