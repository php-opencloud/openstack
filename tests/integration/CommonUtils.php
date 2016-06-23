<?php

namespace OpenStack\Integration;

use GuzzleHttp\Client;

class CommonUtils
{
    public static function toCamelCase($word, $separator = '_')
    {
        return str_replace($separator, '', ucwords($word, $separator));
    }
}
