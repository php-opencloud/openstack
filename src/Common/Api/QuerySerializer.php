<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\Query;
use GuzzleHttp\Url;

/**
 * Class responsible for populating the URL query of a {@see GuzzleHttp\Message\Request} object.
 *
 * @package OpenStack\Common\Api
 */
class QuerySerializer
{
    /**
     * @param array       $userValues  The user-defined values that will populate the JSON
     * @param []Parameter $params      The parameter schemas that define how each value is populated.
     *                                 For example, specifying any deep nesting or aliasing.
     * @param string      $inputString The initial URL string being decorated.
     *
     * @return Url
     */
    public function serialize($userValues, array $params, $inputString)
    {
        $url = Url::fromString($inputString);

        $query = new Query();

        foreach ($userValues as $paramName => $value) {
            $schema = $params[$paramName];
            if (!$schema->hasLocation('query')) {
                continue;
            }

            $query->set($schema->getName(), $value);
        }

        $url->setQuery($query);

        return $url;
    }
} 
