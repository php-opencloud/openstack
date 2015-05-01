<?php

namespace OpenStack\Common\Api;

use OpenStack\Common\JsonPath;

/**
 * Class responsible for populating the JSON body of a {@see GuzzleHttp\Message\Request} object.
 *
 * @package OpenStack\Common\Api
 */
class JsonSerializer
{
    /**
     * Populates the actual value into a JSON field, i.e. it has reached the end of the line and no
     * further nesting is required.
     *
     * @param mixed     $userValue The user value that is populating a JSON field
     * @param Parameter $param     The schema that defines how the JSON field is being populated
     * @param array     $json      The existing JSON structure that will be populated
     *
     * @return array|mixed
     */
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

    /**
     * Populates a value into an array-like structure.
     *
     * @param mixed     $userValue The user value that is populating a JSON field
     * @param Parameter $param     The schema that defines how the JSON field is being populated
     *
     * @return array|mixed
     */
    private function stockArrayJson($userValue, Parameter $param)
    {
        $elems = [];

        foreach ($userValue as $item) {
            $elems = $this->stockJson($item, $param->getItemSchema(), $elems);
        }

        return $elems;
    }

    /**
     * Populates a value into an object-like structure.
     *
     * @param mixed     $userValue The user value that is populating a JSON field
     * @param Parameter $param     The schema that defines how the JSON field is being populated
     *
     * @return array
     */
    private function stockObjectJson($userValue, Parameter $param)
    {
        $object = [];

        foreach ($userValue as $key => $val) {
            $object = $this->stockJson($val, $param->getProperty($key), $object);
        }

        return $object;
    }

    /**
     * A generic method that will populate a JSON structure with a value according to a schema. It
     * supports multiple types and will delegate accordingly.
     *
     * @param mixed     $userValue The user value that is populating a JSON field
     * @param Parameter $param     The schema that defines how the JSON field is being populated
     * @param array     $json      The existing JSON structure that will be populated
     *
     * @return array
     */
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

    /**
     * @param array       $userValues The user-defined values that will populate the JSON
     * @param []Parameter $params     The parameter schemas that define how each value is populated.
     *                                For example, specifying any deep nesting or aliasing.
     * @param array       $options    Configuration options which also specify how the JSON is
     *                                structured. Currently this is restricted to a top-level key.
     *
     * @return array
     */
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