<?php

namespace OpenStack\Compute\v2\Api;

class Image
{
    public static function getAll()
    {
        return [
            'method' => 'GET',
            'path'   => 'images',
            'params' => [
                'changesSince' => [
                    'type' => 'string',
                    'location' => 'query',
                    'sentAs' => 'changes-since',
                ],
                'server' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'status' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'type' => [
                    'type' => 'string',
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
            'path'   => 'images/{id}',
            'params' => [CommonParams::$id]
        ];
    }

    public static function delete()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}',
            'params' => ['id' => CommonParams::$id]
        ];
    }
} 