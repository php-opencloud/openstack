<?php

namespace OpenStack\Compute\v2;

final class Api
{
    public static function postServers()
    {
        return [
            'path' => 'servers',
            'method' => 'POST',
            'jsonKey' => 'server',
            'params' => [
                'securityGroups' => [
                    'type' => 'array',
                    'items' => ['type' => 'object', 'items' => ['name' => ['type' => 'string']]],
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
                        'items' => [
                            'uuid' => ['type' => 'string'],
                            'port' => ['type' => 'string'],
                        ]
                    ]
                ],
                'name' => ['type' => 'string', 'required' => true],
                'metadata' => ['type' => 'string'],
                'personality' => ['type' => 'string'],
                'blockDeviceMapping' => [
                    'type' => 'array',
                    'sentAs' => 'block_device_mapping_v2',
                    'items' => [
                        'type' => 'object',
                        'items' => [
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

    public static function headServers()
    {

    }
}