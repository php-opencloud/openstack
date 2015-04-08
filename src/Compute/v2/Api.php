<?php

namespace OpenStack\Compute\v2;

class Api
{
    private static $idParam = ['type' => 'string', 'required' => true, 'location' => 'url'];
    private static $keyParam = ['type' => 'string', 'location' => 'url', 'required' => true];
    private static $metadataParam = ['type' => 'object', 'location' => 'json', 'required' => true];

    public static function getFlavors()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors',
            'params' => [
                'minDisk' => ['type' => 'integer', 'location' => 'query'],
                'minRam' => ['type' => 'integer', 'location' => 'query'],
                'limit' => ['type' => 'integer', 'location' => 'query'],
                'marker' => ['type' => 'string', 'location' => 'query'],
            ],
        ];
    }

    public static function getFlavorsDetail()
    {
        $op = self::getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public static function getFlavor()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors/{id}',
            'params' => [self::$idParam]
        ];
    }

    public static function getImages()
    {
        return [
            'method' => 'GET',
            'path'   => 'images',
            'params' => [
                'changesSince' => ['type' => 'string', 'location' => 'query', 'sentAs' => 'changes-since'],
                'server' => ['type' => 'string', 'location' => 'query'],
                'name'   => ['type' => 'string', 'location' => 'query'],
                'status' => ['type' => 'string', 'location' => 'query'],
                'type'   => ['type' => 'string', 'location' => 'query'],
                'limit'  => ['type' => 'integer', 'location' => 'query'],
                'marker' => ['type' => 'string', 'location' => 'query'],
            ],
        ];
    }

    public static function getImagesDetail()
    {
        $op = self::getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public static function getImage()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}',
            'params' => [self::$idParam]
        ];
    }

    public static function deleteImage()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}',
            'params' => ['id' => self::$idParam]
        ];
    }

    public static function getImageMetadata()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata',
            'params' => ['id' => self::$idParam]
        ];
    }

    public static function putImageMetadata()
    {
        return [
            'method' => 'PUT',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id' => self::$idParam,
                'metadata' => self::$metadataParam
            ]
        ];
    }

    public static function postImageMetadata()
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id' => self::$idParam,
                'metadata' => self::$metadataParam
            ]
        ];
    }

    public static function getImageMetadataKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id' => self::$idParam,
                'key' => self::$keyParam,
            ]
        ];
    }

    public static function deleteImageMetadataKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id' => self::$idParam,
                'key' => self::$keyParam,
            ]
        ];
    }

    public static function postServer()
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
                'metadata' => ['type' => 'object', 'location' => 'json'],
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

    public static function getServers()
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

    public static function getServersDetail()
    {
        $definition = self::getServers();
        $definition['path'] += '/detail';
        return $definition;
    }

    public static function getServer()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}',
            'params' => ['id' => self::$idParam]
        ];
    }

    public static function putServer()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}',
            'params' => [
                'id'   => self::$idParam,
                'name' => ['type' => 'string', 'location' => 'json'],
                'ipv4' => ['type' => 'string','location' => 'json'],
                'ipv6' => ['type' => 'string','location' => 'json'],
            ],
        ];
    }

    public static function deleteServer()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}',
            'params' => ['id' => self::$idParam],
        ];
    }

    public static function changeServerPassword()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'changePassword',
            'params' => [
                'id' => self::$idParam,
                'password' => ['sentAs' => 'adminPass', 'type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function rebootServer()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'reboot',
            'params' => [
                'id' => self::$idParam,
                'type' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function rebuildServer()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => ['id' => self::$idParam],
        ];
    }

    public static function resizeServer()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'resize',
            'params' => [
                'id' => self::$idParam,
                'flavorId' => ['sentAs' => 'flavorRef', 'type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function confirmServerResize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => [
                'id' => self::$idParam,
                'confirmResize' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function revertServerResize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => [
                'id' => self::$idParam,
                'revertResize' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public static function createServerImage()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'createImage',
            'params' => [
                'id' => self::$idParam,
                'name' => ['type' => 'string', 'required' => true, 'location' => 'json'],
                'metadata' => self::$metadataParam,
            ],
        ];
    }

    public static function getAddresses()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips',
            'params' => ['id' => self::$idParam],
        ];
    }

    public static function getAddressesByNetwork()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips/{networkLabel}',
            'params' => [
                'id' => self::$idParam,
                'networkLabel' => ['type' => 'string', 'location' => 'url', 'required' => true],
            ],
        ];
    }

    public static function getServerMetadata()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata',
            'params' => ['id' => self::$idParam]
        ];
    }

    public static function putServerMetadata()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id' => self::$idParam,
                'metadata' => self::$metadataParam
            ]
        ];
    }

    public static function postServerMetadata()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id' => self::$idParam,
                'metadata' => self::$metadataParam
            ]
        ];
    }

    public static function getServerMetadataKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => self::$idParam,
                'key' => self::$keyParam,
            ]
        ];
    }

    public static function deleteServerMetadataKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => self::$idParam,
                'key' => self::$keyParam,
            ]
        ];
    }
}