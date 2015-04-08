<?php

namespace OpenStack\Compute\v2\Api;

class ImageMetadata
{
    public static function get()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata',
            'params' => ['id' => CommonParams::$id]
        ];
    }

    public static function put()
    {
        return [
            'method' => 'PUT',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id' => CommonParams::$id,
                'metadata' => [
                    'type' => 'object',
                    'location' => 'json',
                ]
            ]
        ];
    }

    public static function post()
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id' => CommonParams::$id,
                'metadata' => [
                    'type' => 'object',
                    'location' => 'json',
                ]
            ]
        ];
    }

    public static function getKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id' => CommonParams::$id,
                'key' => CommonParams::$key,
            ]
        ];
    }

    public static function deleteKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id' => CommonParams::$id,
                'key' => CommonParams::$key,
            ]
        ];
    }
}