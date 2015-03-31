<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use OpenStack\Common\JsonPath;

class Operation
{
    private $client;
    private $definition;
    private $userOptions;

    public function __construct(ClientInterface $client, array $definition, array $userOptions)
    {
        $this->client = $client;
        $this->definition = $definition;
        $this->userOptions = $userOptions;
    }

    public function validate()
    {
        // Check for undefined keys
        if (!empty($disallowedKeys = array_keys(array_diff_key($this->userOptions, $this->definition['params'])))) {
            throw new \Exception(sprintf(
                'The following keys are not supported: %s', implode($disallowedKeys, ', ')
            ));
        }

        foreach ($this->definition['params'] as $paramName => $paramSchema) {
            // Check for required options
            if (isset($paramSchema['required']) && $paramSchema['required'] && !isset($this->userOptions[$paramName])) {
                throw new \Exception(sprintf(
                    '"%s" is a required option, but it was not provided', $paramName
                ));
            }

            if (isset($this->userOptions[$paramName])) {
                $this->validateParam($paramName, $this->userOptions[$paramName], $paramSchema);
            }
        }

        return true;
    }

    public function createRequest()
    {
        $this->validate();

        $headers = $json = $options = [];

        foreach ($this->userOptions as $paramName => $value) {
            $schema = $this->definition['params'][$paramName];
            if (isset($schema['location']) && $schema['location'] == 'header') {
                $headers = $this->stockHeader($paramName, $value, $schema, $headers);
            } else {
                $json = $this->stockJson($paramName, $value, $schema, $json);
            }
        }

        if (!empty($headers)) {
            $options['headers'] = $headers;
        }
        if (!empty($json)) {
            $options['json'] = isset($this->definition['jsonKey']) ? [$this->definition['jsonKey'] => $json] : $json;
        }

        return $this->client->createRequest($this->definition['method'], $this->definition['path'], $options);
    }

    private function isAssociative(array $array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    private function checkType($userValue, $type)
    {
        // For params defined as objects, we'll let the user get away with
        // passing in an associative array - since it's effectively a hash
        if ($type == 'object' && $this->isAssociative($userValue)) {
            return true;
        }

        return gettype($userValue) == $type;
    }

    private function stockHeader($attrName, $userValue, $schema, $headers)
    {
        if ($attrName == 'metadata') {
            foreach ($userValue as $key => $keyVal) {
                $headers = $this->stockHeader($key, $keyVal, $schema['items'], $headers);
            }
        }

        if (is_string($userValue) || is_numeric($userValue)) {
            $name = isset($schema['sentAs']) ? $schema['sentAs'] : $attrName;
            if (!empty($schema['prefix'])) {
                $name = $schema['prefix'] . $name;
            }
            $headers[$name] = $userValue;
        }

        return $headers;
    }

    private function stockJson($attrName, $userValue, $schema, $json)
    {
        $name = isset($schema['sentAs']) ? $schema['sentAs'] : $attrName;

        // Type check nested array elements
        if ($schema['type'] == 'array' && isset($schema['items'])) {
            $elems = [];
            foreach ($userValue as $item) {
                $elems = $this->stockJson(null, $item, $schema['items'], $elems);
            }
            $json[$name] = $elems;
        }

        // Type check nested object keys
        if ($schema['type'] == 'object' && isset($schema['properties'])) {
            $object = [];
            foreach ($userValue as $key => $keyVal) {
                $object = $this->stockJson($key, $keyVal, $schema['properties'][$key], $object);
            }
            $json[$name] = $object;
        }

        if (is_scalar($userValue)) {
            if (isset($schema['path'])) {
                $jsonPath = new JsonPath($json);
                $jsonPath->set(sprintf("%s.%s", $schema['path'], $name), $userValue);
                $json = $jsonPath->getStructure();
            } elseif ($attrName) {
                $json[$name] = $userValue;
            } else {
                $json[] = $userValue;
            }
        }

        return $json;
    }

    private function validateParam($attrName, $userValue, $schema)
    {
        // Type checking
        if (isset($schema['type']) && is_string($schema['type'])
            && false === $this->checkType($userValue, $schema['type'])
        ) {
            throw new \Exception(sprintf(
                'The key provided "%s" has the wrong value type. Your provided %s but was expecting %s',
                $attrName, print_r($userValue, true), $schema['type']
            ));
        }

        // Type check nested array elements
        if (isset($schema['type']) && $schema['type'] == 'array' && isset($schema['items'])) {
            foreach ($userValue as $item) {
                $this->validateParam($attrName . '[]', $item, $schema['items']);
            }
        }

        // Type check nested object keys
        if (isset($schema['type']) && $schema['type'] == 'object' && isset($schema['properties'])) {
            foreach ($userValue as $key => $keyVal) {

                // Check that nested keys are properly defined, but
                // permit arbitrary structures if it's metadata
                if (!isset($schema['properties'][$key])) {
                    if ($attrName == 'metadata') {
                        $_schema = $schema['properties'];
                    } else {
                        throw new \Exception(sprintf(
                            'The key provided "%s" is not defined', $key
                        ));
                    }
                } else {
                    $_schema = $schema['properties'][$key];
                }

                $this->validateParam($key, $keyVal, $_schema);
            }
        }
    }
}
