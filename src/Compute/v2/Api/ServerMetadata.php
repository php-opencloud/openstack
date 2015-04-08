<?php

namespace OpenStack\Compute\v2\Api;

class ServerMetadata
{
    public static function get()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata',
            'params' => ['id' => CommonParams::$id]
        ];
    }

    public static function put()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id' => CommonParams::$id,
                'metadata' => ['type' => 'object', 'location' => 'json']
            ]
        ];
    }

    public static function post()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id' => CommonParams::$id,
                'metadata' => ['type' => 'object', 'location' => 'json']
            ]
        ];
    }

    public static function getKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => CommonParams::$id,
                'key' => CommonParams::$key,
            ]
        ];
    }

    public static function deleteKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => CommonParams::$id,
                'key' => CommonParams::$key,
            ]
        ];
    }
}