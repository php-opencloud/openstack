<?php

namespace OpenStack\Compute\v2\Api;

class Addresses
{
    public static function listAll()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips',
            'params' => [
                'id' => CommonParams::$id
            ],
        ];
    }

    public static function listByNetwork()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips/{networkLabel}',
            'params' => [
                'id' => CommonParams::$id,
                'networkLabel' => [
                    'type' => 'string',
                    'location' => 'url',
                    'required' => true,
                ],
            ],
        ];
    }
} 