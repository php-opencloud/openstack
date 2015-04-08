<?php

namespace OpenStack\Common\Api;

class HeaderSerializer
{
    private $headers = [];

    private function stockHeader(Parameter $param, $name, $value)
    {
        if ($name == 'metadata') {
            foreach ($value as $key => $keyVal) {
                $this->stockHeader($param->getItemSchema(), $key, $keyVal);
            }
        }

        if (is_string($value) || is_numeric($value)) {
            if ($prefix = $param->getPrefix()) {
                $name = $prefix . $name;
            }
            $this->headers[$name] = $value;
        }
    }

    public function serialize($userValues, array $params)
    {
        foreach ($userValues as $paramName => $value) {
            $schema = $params[$paramName];
            if (!$schema->hasLocation('header')) {
                continue;
            }
            $this->stockHeader($schema, $schema->getName(), $value);
        }

        return $this->headers;
    }
}
