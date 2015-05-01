<?php

namespace OpenStack\Common\Api;

/**
 * Class responsible for populating the headers of a {@see GuzzleHttp\Message\Request} object.
 *
 * @package OpenStack\Common\Api
 */
class HeaderSerializer
{
    /** @var array */
    private $headers = [];

    /**
     * @param Parameter $param The schema of the parameter being populated
     * @param string    $name  The parameter/header name
     * @param mixed     $value The user-defined header value
     */
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

    /**
     * @param array       $userValues The user-defined values being populated
     * @param []Parameter $params     The parameter schemas which defines how headers are populated
     *
     * @return array
     */
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
