<?php

namespace OpenStack\Common\Api;

class HeaderSerializer
{
    private $headers = [];

    private function stockHeader($userValue, Parameter $param)
    {
        $name = $param->getName();

        if ($name == 'metadata') {
            foreach ($userValue as $key => $keyVal) {
                $this->stockHeader($keyVal, $param->getItemSchema());
            }
        }

        if (is_string($userValue) || is_numeric($userValue)) {
            if ($prefix = $param->getPrefix()) {
                $name = $prefix . $name;
            }
            $this->headers[$name] = $userValue;
        }
    }

    public function serialize($userValues, array $params)
    {
        foreach ($userValues as $paramName => $value) {
            $schema = $params[$paramName];
            if (!$schema->hasLocation('header')) {
                continue;
            }
            $this->stockHeader($value, $schema);
        }

        return $this->headers;
    }
}
