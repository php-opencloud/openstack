<?php

namespace OpenStack\Common\Api;

class HeaderSerializer
{
    private $headers = [];

    private function stockHeader($userValue, array $schema)
    {
        $name = $schema['name'];

        if ($name == 'metadata') {
            foreach ($userValue as $key => $keyVal) {
                $schema = $schema['items'] + ['name' => $key];
                $this->stockHeader($keyVal, $schema);
            }
        }

        if (is_string($userValue) || is_numeric($userValue)) {
            $name = isset($schema['sentAs']) ? $schema['sentAs'] : $name;
            if (!empty($schema['prefix'])) {
                $name = $schema['prefix'] . $name;
            }
            $this->headers[$name] = $userValue;
        }
    }

    public function serialize($userValues, array $definition)
    {
        foreach ($userValues as $paramName => $value) {
            $schema = $definition['params'][$paramName];

            if (!isset($schema['location']) || $schema['location'] != 'header') {
                continue;
            }

            $schema['name'] = $paramName;
            $this->stockHeader($value, $schema);
        }

        return $this->headers;
    }
}
