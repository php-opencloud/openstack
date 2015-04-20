<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\Query;
use GuzzleHttp\Url;

class QuerySerializer
{
    public function serialize($userValues, array $params, $inputString)
    {
        $url = Url::fromString($inputString);

        $query = new Query();

        foreach ($userValues as $paramName => $value) {
            $schema = $params[$paramName];
            if (!$schema->hasLocation('query')) {
                continue;
            }

            $query->set($paramName, $value);
        }

        $url->setQuery($query);

        return $url;
    }
} 