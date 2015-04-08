<?php

namespace OpenStack\Compute\v2\Api;

class Server
{
    public static function post()
    {
        return [
            'path' => 'servers',
            'method' => 'POST',
            'jsonKey' => 'server',
            'params' => [
                'securityGroups' => [
                    'type' => 'array',
                    'items' => ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]],
                    'sentAs' => 'security_groups',
                ],
                'userData' => ['type' => 'string', 'sentAs' => 'user_data'],
                'availabilityZone' => ['type' => 'string', 'sentAs' => 'availability_zone'],
                'imageId' => ['type' => 'string', 'required' => true, 'sentAs' => 'imageRef'],
                'flavorId' => ['type' => 'string', 'required' => true, 'sentAs' => 'flavorRef'],
                'networks' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'uuid' => ['type' => 'string'],
                            'port' => ['type' => 'string'],
                        ]
                    ]
                ],
                'name' => ['type' => 'string', 'required' => true],
                'metadata' => ['type' => 'object'],
                'personality' => ['type' => 'string'],
                'blockDeviceMapping' => [
                    'type' => 'array',
                    'sentAs' => 'block_device_mapping_v2',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'configDrive' => ['type' => 'string', 'sentAs' => 'config_drive'],
                            'bootIndex' => ['type' => 'string', 'sentAs' => 'boot_index'],
                            'deleteOnTermination' => ['type' => 'boolean', 'sentAs' => 'delete_on_termination'],
                            'guestFormat' => ['type' => 'string', 'sentAs' => 'guest_format'],
                            'destinationType' => ['type' => 'string', 'sentAs' => 'destination_type'],
                            'sourceType' => ['type' => 'string', 'sentAs' => 'source_type'],
                            'deviceName' => ['type' => 'string', 'sentAs' => 'device_name'],
                        ]
                    ],
                ],
            ]
        ];
    }

    public static function getAll()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers',
            'params' => [
                'changesSince' => ['sentAs' => 'changes-since', 'type' => 'string', 'location' => 'query'],
                'imageId'      => ['sentAs' => 'image', 'type' => 'string', 'location' => 'query'],
                'flavorId'     => ['sentAs' => 'flavor', 'type' => 'string', 'location' => 'query'],
                'name'         => ['type' => 'string', 'location' => 'query'],
                'marker'       => ['type' => 'string', 'location' => 'query'],
                'limit'        => ['type' => 'string', 'location' => 'query'],
                'status'       => ['type' => 'string', 'location' => 'query'],
                'host'         => ['type' => 'string', 'location' => 'query']
            ],
        ];
    }

    public static function getAllDetail()
    {
        $definition = self::getServers();
        $definition['path'] += '/detailed';
        return $definition;
    }

    public static function get()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}',
            'params' => ['id' => CommonParams::$id]
        ];
    }

    public static function put()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}',
            'params' => [
                'id'   => CommonParams::$id,
                'name' => ['type' => 'string', 'location' => 'json'],
                'ipv4' => ['type' => 'string','location' => 'json'],
                'ipv6' => ['type' => 'string','location' => 'json'],
            ],
        ];
    }

    public static function delete()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}',
            'params' => ['id' => CommonParams::$id],
        ];
    }

    public static function changePassword()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'changePassword',
            'params' => [
                'id' => CommonParams::$id,
                'password' => ['sentAs' => 'adminPass', 'type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function reboot()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'reboot',
            'params' => [
                'id' => CommonParams::$id,
                'type' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function rebuild()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => ['id' => CommonParams::$id],
        ];
    }

    public static function resize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'resize',
            'params' => [
                'id' => CommonParams::$id,
                'flavorId' => ['sentAs' => 'flavorRef', 'type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function confirmResize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => [
                'id' => CommonParams::$id,
                'confirmResize' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function revertResize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => [
                'id' => CommonParams::$id,
                'revertResize' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function createImage()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'createImage',
            'params' => [
                'id' => CommonParams::$id,
                'name'     => ['type' => 'string', 'required' => true, 'location' => 'json'],
                'metadata' => ['type' => 'object', 'location' => 'json'],
            ],
        ];
    }
}