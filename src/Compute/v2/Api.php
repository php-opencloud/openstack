<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Api\ApiInterface;

/**
 * A representation of the Compute (Nova) v2 REST API.
 *
 * @internal
 * @package OpenStack\Compute\v2
 */
class Api implements ApiInterface
{
    private $idParam = [
        'type' => 'string',
        'required' => true,
        'location' => 'url',
        'description' => 'The unique ID of the remote resource.',
    ];

    private $nameParam = [
        'type' => 'string',
        'location' => 'json',
        'description' => 'The name of the resource',
    ];

    private $keyParam = [
        'type' => 'string',
        'location' => 'url',
        'required' => true,
        'description' => 'The specific metadata key you are interacting with',
    ];

    private $ipv4Param = [
        'type' => 'string',
        'location' => 'json',
        'sentAs' => 'accessIPv4',
        'description' => 'The IP address (version 4) of the remote resource',
    ];

    private $ipv6Param = [
        'type' => 'string',
        'location' => 'json',
        'sentAs' => 'accessIPv6',
        'description' => 'The IP address (version 6) of the remote resource',
    ];

    private $imageIdParam = [
        'type' => 'string',
        'required' => true,
        'sentAs' => 'imageRef',
        'description' => 'The unique ID of the image that this server will be based on',
    ];

    private $flavorIdParam = [
        'type' => 'string',
        'required' => true,
        'sentAs' => 'flavorRef',
        'description' => 'The unique ID of the flavor that this server will be based on',
    ];

    private $metadataParam = [
        'type' => 'object',
        'location' => 'json',
        'required' => true,
        'description' => 'An arbitrary key/value pairing that will be used for metadata.',
        'properties' => [
            'type' => 'string',
            'description' => 'The value being set for your key. Bear in mind that "key" is just an example, you can name it anything.'
        ]
    ];

    private $personalityParam = [
        'type' => 'array',
        'description' => <<<EOL
File path and contents (text only) to inject into the server at launch. The maximum size of the file path data is 255
bytes. The maximum limit refers to the number of bytes in the decoded data and not the number of characters in the
encoded data.
EOL
        ,
        'items' => [
            'type' => 'object',
            'properties' => [
                'path' => [
                    'type' => 'string',
                    'description' => 'The path, on the filesystem, where the personality file will be placed'
                ],
                'contents' => [
                    'type' => 'string',
                    'description' => 'Base64-encoded content of the personality file'
                ],
            ]
        ]
    ];

    private $limitParam = [
        'type'     => 'integer',
        'location' => 'query',
        'description' => <<<DESC
This will limit the total amount of elements returned in a list up to the number specified. For example, specifying a
limit of 10 will return 10 elements, regardless of the actual count.
DESC
    ];

    private $markerParam = [
        'type'     => 'string',
        'location' => 'query',
        'description' => <<<DESC
Specifying a marker will begin the list from the value specified. Elements will have a particular attribute that
identifies them, such as a name or ID. The marker value will search for an element whose identifying attribute matches
the marker value, and begin the list from there.
DESC
    ];

    public function getFlavors()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors',
            'params' => [
                'limit'   => $this->limitParam,
                'marker'  => $this->markerParam,
                'minDisk' => [
                    'type' => 'integer',
                    'location' => 'query',
                    'description' => 'Return flavors that have a minimum disk space in GB.',
                ],
                'minRam'  => [
                    'type' => 'integer',
                    'location' => 'query',
                    'description' => 'Return flavors that have a minimum RAM size in GB.',
                ],
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
                'limit'  => $this->limitParam,
                'marker' => $this->markerParam,
                'name'   => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => 'Return images which match a certain name.',
                ],
                'changesSince' => [
                    'type' => 'string',
                    'location' => 'query',
                    'sentAs' => 'changes-since',
                    'description' => 'Return images which have been changed since a certain time. This value needs to be in an ISO 8601 format.',
                ],
                'server' => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => 'Return images which are associated with a server. This value needs to be in a URL format.',
                ],
                'status' => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => 'Return images that have a particular status, such as "ACTIVE".',
                ],
                'type'   => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => 'Return images that are of a particular type, such as "snapshot" or "backup".',
                ],
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
            'params' => ['id' => $this->idParam]
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
                'imageId'     => $this->imageIdParam,
                'flavorId'    => $this->flavorIdParam,
                'personality' => $this->personalityParam,
                'metadata'    => $this->notRequired($this->metadataParam),
                'name'        => $this->isRequired($this->nameParam),
                'securityGroups' => [
                    'type' => 'array',
                    'sentAs' => 'security_groups',
                    'description' => 'A list of security group objects which this server will be associated with',
                    'items' => [
                        'type'       => 'object',
                        'properties' => ['name' => $this->nameParam]
                    ],
                ],
                'userData' => [
                    'type' => 'string',
                    'sentAs' => 'user_data',
                    'description' => 'Configuration information or scripts to use upon launch. Must be Base64 encoded.',
                ],
                'availabilityZone' => [
                    'type' => 'string',
                    'sentAs' => 'availability_zone',
                    'description' => 'The availability zone in which to launch the server.',
                ],
                'networks' => [
                    'type' => 'array',
                    'description' => <<<EOT
A list of network objects which this server will be associated with. By default, the server instance is provisioned
with all isolated networks for the tenant. Optionally, you can create one or more NICs on the server.

To provision the server instance with a NIC for a network, specify the UUID of the network in the uuid attribute in a
networks object.

To provision the server instance with a NIC for an already existing port, specify the port-id in the port attribute in
a networks object.
EOT
                    ,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'uuid' => [
                                'type' => 'string',
                                'description' => <<<EOL
To provision the server instance with a NIC for a network, specify the UUID of the network in the uuid attribute in a
networks object. Required if you omit the port attribute
EOL
                            ],
                            'port' => [
                                'type' => 'string',
                                'description' => <<<EOL
To provision the server instance with a NIC for an already existing port, specify the port-id in the port attribute in
a networks object. The port status must be DOWN. Required if you omit the uuid attribute.
EOL
                            ],
                        ]
                    ]
                ],
                'blockDeviceMapping' => [
                    'type' => 'array',
                    'sentAs' => 'block_device_mapping_v2',
                    'description' => <<<EOL
Enables booting the server from a volume when additional parameters are given. If specified, the volume status must be
available, and the volume attach_status in OpenStack Block Storage DB must be detached.
EOL
                    ,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'configDrive' => [
                                'type' => 'bool',
                                'sentAs' => 'config_drive',
                                'description' => 'Enables metadata injection in a server through a configuration drive. To enable a configuration drive, specify true. Otherwise, specify false.',
                            ],
                            'bootIndex' => [
                                'type' => 'integer',
                                'sentAs' => 'boot_index',
                                'description' => 'Indicates a number designating the boot order of the device. Use -1 for the boot volume, choose 0 for an attached volume.',
                            ],
                            'deleteOnTermination' => [
                                'type' => 'boolean',
                                'sentAs' => 'delete_on_termination',
                                'description' => 'To delete the boot volume when the server stops, specify true. Otherwise, specify false.',
                            ],
                            'guestFormat' => [
                                'type' => 'string',
                                'sentAs' => 'guest_format',
                                'description' => 'Specifies the guest server disk file system format, such as "ephemeral" or "swap".',
                            ],
                            'destinationType' => [
                                'type' => 'string',
                                'sentAs' => 'destination_type',
                                'description' => 'Describes where the volume comes from. Choices are "local" or "volume". When using "volume" the volume ID',
                            ],
                            'sourceType' => [
                                'type' => 'string',
                                'sentAs' => 'source_type',
                                'description' => 'Describes the volume source type for the volume. Choices are "blank", "snapshot", "volume", or "image".',
                            ],
                            'deviceName' => [
                                'type' => 'string',
                                'sentAs' => 'device_name',
                                'description' => 'Describes a path to the device for the volume you want to use to boot the server.',
                            ],
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
                'limit'        => $this->limitParam,
                'marker'       => $this->markerParam,
                'changesSince' => [
                    'sentAs' => 'changes-since',
                    'type' => 'string',
                    'location' => 'query',
                    'description' => '',
                ],
                'imageId'      => [
                    'sentAs' => 'image',
                    'type' => 'string',
                    'location' => 'query',
                    'description' => '',
                ],
                'flavorId'     => [
                    'sentAs' => 'flavor',
                    'type' => 'string',
                    'location' => 'query',
                    'description' => '',
                ],
                'name'         => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => '',
                ],
                'status'       => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => '',
                ],
                'host'         => [
                    'type' => 'string',
                    'location' => 'query',
                    'description' => '',
                ]
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
                'ipv4' => $this->ipv4Param,
                'ipv6' => $this->ipv6Param,
                'name' => $this->nameParam,
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
                'password' => [
                    'sentAs' => 'adminPass',
                    'type' => 'string',
                    'location' => 'json',
                    'required' => true,
                    'description' => '',
                ],
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
                'type' => [
                    'type' => 'string',
                    'location' => 'json',
                    'required' => true,
                    'description' => '',
                ],
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
                'ipv4'        => $this->ipv4Param,
                'ipv6'        => $this->ipv6Param,
                'imageId'     => $this->imageIdParam,
                'personality' => $this->personalityParam,
                'name'        => $this->nameParam,
                'metadata'    => $this->notRequired($this->metadataParam),
                'adminPass'   => [
                    'type' => 'string',
                    'location' => 'json',
                    'description' => '',
                ],
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
                'flavorId' => $this->flavorIdParam,
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
                'confirmResize' => [
                    'type' => 'NULL',
                    'location' => 'json',
                    'required' => true
                ],
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
                'revertResize' => [
                    'type' => 'NULL',
                    'location' => 'json',
                    'required' => true
                ],
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
                'metadata' => $this->notRequired($this->metadataParam),
                'name'     => $this->isRequired($this->nameParam),
            ],
        ];
    }

    public function getAddresses()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips',
            'params' => [
                'id' => $this->idParam
            ],
        ];
    }

    public function getAddressesByNetwork()
    {
        return [
            'method' => 'GET',
            'path' => 'servers/{id}/ips/{networkLabel}',
            'params' => [
                'id' => $this->idParam,
                'networkLabel' => [
                    'type' => 'string',
                    'location' => 'url',
                    'required' => true,
                ],
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

    private function isRequired(array $param)
    {
        return array_merge($param, ['required' => true]);
    }

    private function notRequired(array $param)
    {
        return array_merge($param, ['required' => false]);
    }
}