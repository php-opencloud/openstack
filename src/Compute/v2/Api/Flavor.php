<?php

namespace OpenStack\Compute\v2\Api;

class Flavor
{
    public static function getAll()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors',
            'params' => [
                'minDisk' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'minRam' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'limit' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'marker' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
            ],
        ];
    }

    public static function getAllDetailed()
    {
        $op = self::getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public static function get()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors/{id}',
            'params' => [CommonParams::$id]
        ];
    }
} 