<?php

namespace OpenStack\Common\Resource;

use GuzzleHttp\Message\ResponseInterface;

trait ResourceTrait
{
    /** @var ResponseInterface */
    public $lastResponse;

    protected $aliases = [];

    public function getServiceNamespace()
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
        $this->setLastResponse($response);

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

    public function setLastResponse(ResponseInterface $response)
    {
        $this->lastResponse = $response;
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