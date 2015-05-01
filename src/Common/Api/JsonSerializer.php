<?php

namespace OpenStack\Common\Api;

use OpenStack\Common\JsonPath;

class JsonSerializer
{
    private function stockValue($userValue, Parameter $param, $json)
    {
        $name = $param->getName();

        if ($path = $param->getPath()) {
            $jsonPath = new JsonPath($json);
            $jsonPath->set(sprintf("%s.%s", $path, $name), $userValue);
            $json = $jsonPath->getStructure();
        } elseif ($name) {
            $json[$name] = $userValue;
        } else {
            $json[] = $userValue;
        }

        return $json;
    }

    private function stockArrayJson($userValue, Parameter $param)
    {
        $elems = [];

        foreach ($userValue as $item) {
            $elems = $this->stockJson($item, $param->getItemSchema(), $elems);
        }

        return $elems;
    }

    private function stockObjectJson($userValue, Parameter $param)
    {
        $object = [];

        foreach ($userValue as $key => $val) {
            $object = $this->stockJson($val, $param->getProperty($key), $object);
        }

        return $object;
    }
    
    private function stockJson($userValue, Parameter $param, $json)
    {
        if ($param->isArray()) {
            $userValue = $this->stockArrayJson($userValue, $param);
        } elseif ($param->isObject()) {
            $userValue = $this->stockObjectJson($userValue, $param);
        }

        // Populate the final value
        return $this->stockValue($userValue, $param, $json);
    }

    public function serialize($userValues, array $params, array $options = [])
    {
        $json = [];

        foreach ($userValues as $paramName => $value) {
            $param = $params[$paramName];
            if (!$param->hasLocation('json')) {
                continue;
            }
            $json = $this->stockJson($value, $param, $json);
        }

        if (isset($options['jsonKey'])) {
            $json = [$options['jsonKey'] => $json];
        }

        return $json;
    }
}