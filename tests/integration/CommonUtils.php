<?php

namespace OpenCloud\Integration;

use GuzzleHttp\Client;

class Utils
{
    public static function toCamelCase($word, $separator = '_')
    {
        return str_replace($separator, '', ucwords($word, $separator));
    }
}
