<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Api\ApiInterface;

/**
 * A representation of the Compute (Nova) v2 REST API.
 *
 * @package OpenStack\Compute\v2
 */
class Api implements ApiInterface
{
    private $idParam = [
        'type' => 'string',
        'required' => true,
        'location' => 'url'
    ];

    private $keyParam = [
        'type' => 'string',
        'location' => 'url',
        'required' => true
    ];

    private $ipv4Param = [
        'type' => 'string',
        'location' => 'json',
        'sentAs' => 'accessIPv4'
    ];

    private $ipv6Param = [
        'type' => 'string',
        'location' => 'json',
        'sentAs' => 'accessIPv6'
    ];

    private $imageIdParam = [
        'type' => 'string',
        'required' => true,
        'sentAs' => 'imageRef'
    ];

    private $flavorIdParam = [
        'type' => 'string',
        'required' => true,
        'sentAs' => 'flavorRef'
    ];

    private $metadataParam = [
        'type' => 'object',
        'location' => 'json',
        'required' => true,
        'properties' => [
            'type' => 'string'
        ]
    ];

    private $personalityParam = [
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'properties' => [
                'path' => ['type' => 'string'],
                'contents' => ['type' => 'string'],
            ]
        ]
    ];

    public function getFlavors()
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

    public function getFlavorsDetail()
    {
        $op = $this->getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public function getFlavor()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors/{id}',
            'params' => ['id' => $this->idParam]
        ];
    }

    public function getImages()
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

    public function getImagesDetail()
    {
        $op = $this->getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public function getImage()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}',
            'params' => [$this->idParam]
        ];
    }

    public function deleteImage()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}',
            'params' => ['id' => $this->idParam]
        ];
    }

    public function getImageMetadata()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata',
            'params' => ['id' => $this->idParam]
        ];
    }

    public function putImageMetadata()
    {
        return [
            'method' => 'PUT',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id' => $this->idParam,
                'metadata' => $this->metadataParam
            ]
        ];
    }

    public function postImageMetadata()
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id' => $this->idParam,
                'metadata' => $this->metadataParam
            ]
        ];
    }

    public function getImageMetadataKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id' => $this->idParam,
                'key' => $this->keyParam,
            ]
        ];
    }

    public function deleteImageMetadataKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id' => $this->idParam,
                'key' => $this->keyParam,
            ]
        ];
    }

    public function postServer()
    {
        return [
            'path' => 'servers',
            'method' => 'POST',
            'jsonKey' => 'server',
            'params' => [
                'securityGroups' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string']
                        ]
                    ],
                    'sentAs' => 'security_groups',
                ],
                'userData' => ['type' => 'string', 'sentAs' => 'user_data'],
                'availabilityZone' => ['type' => 'string', 'sentAs' => 'availability_zone'],
                'imageId' => $this->imageIdParam,
                'flavorId' => $this->flavorIdParam,
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
                'personality' => $this->personalityParam,
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

    public function getServers()
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

    public function getServersDetail()
    {
        $definition = $this->getServers();
        $definition['path'] += '/detail';
        return $definition;
    }

    public function getServer()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}',
            'params' => ['id' => $this->idParam]
        ];
    }

    public function putServer()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}',
            'jsonKey' => 'server',
            'params' => [
                'id'   => $this->idParam,
                'name' => ['type' => 'string', 'location' => 'json'],
                'ipv4' => $this->ipv4Param,
                'ipv6' => $this->ipv6Param,
            ],
        ];
    }

    public function deleteServer()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}',
            'params' => ['id' => $this->idParam],
        ];
    }

    public function changeServerPassword()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'changePassword',
            'params' => [
                'id' => $this->idParam,
                'password' => ['sentAs' => 'adminPass', 'type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public function rebootServer()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'reboot',
            'params' => [
                'id' => $this->idParam,
                'type' => ['type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public function rebuildServer()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/action',
            'jsonKey' => 'rebuild',
            'params' => [
                'id'          => $this->idParam,
                'name'        => ['type' => 'string', 'location' => 'json'],
                'ipv4'        => $this->ipv4Param,
                'ipv6'        => $this->ipv6Param,
                'imageId'     => $this->imageIdParam,
                'adminPass'   => ['type' => 'string', 'location' => 'json'],
                'metadata'    => ['type' => 'object', 'location' => 'json', 'properties' => ['type' => 'string']],
                'personality' => $this->personalityParam,
            ],
        ];
    }

    public function resizeServer()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'resize',
            'params' => [
                'id' => $this->idParam,
                'flavorId' => ['sentAs' => 'flavorRef', 'type' => 'string', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public function confirmServerResize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => [
                'id' => $this->idParam,
                'confirmResize' => ['type' => 'NULL', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public function revertServerResize()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'params' => [
                'id' => $this->idParam,
                'revertResize' => ['type' => 'NULL', 'location' => 'json', 'required' => true],
            ],
        ];
    }

    public function createServerImage()
    {
        return [
            'method' => 'POST',
            'path' => 'servers/{id}/action',
            'jsonKey' => 'createImage',
            'params' => [
                'id'       => $this->idParam,
                'name'     => ['type' => 'string', 'required' => true, 'location' => 'json'],
                'metadata' => $this->metadataParam,
            ],
        ];
    }

    public function getAddresses()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips',
            'params' => ['id' => $this->idParam],
        ];
    }

    public function getAddressesByNetwork()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips/{networkLabel}',
            'params' => [
                'id' => $this->idParam,
                'networkLabel' => ['type' => 'string', 'location' => 'url', 'required' => true],
            ],
        ];
    }

    public function getServerMetadata()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata',
            'params' => ['id' => $this->idParam]
        ];
    }

    public function putServerMetadata()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id' => $this->idParam,
                'metadata' => $this->metadataParam
            ]
        ];
    }

    public function postServerMetadata()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id' => $this->idParam,
                'metadata' => $this->metadataParam
            ]
        ];
    }

    public function getServerMetadataKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => $this->idParam,
                'key' => $this->keyParam,
            ]
        ];
    }

    public function deleteServerMetadataKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => $this->idParam,
                'key' => $this->keyParam,
            ]
        ];
    }
}