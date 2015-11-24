<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function urlId($type)
    {
        return parent::id($type) + [
            'required' => true,
            'location' => self::URL,
        ];
    }

    public function minDisk()
    {
        return [
            'type'        => self::INT_TYPE,
            'location'    => self::QUERY,
            'description' => 'Return flavors that have a minimum disk space in GB.',
        ];
    }

    public function minRam()
    {
        return [
            'type'        => self::INT_TYPE,
            'location'    => self::QUERY,
            'description' => 'Return flavors that have a minimum RAM size in GB.',
        ];
    }

    public function flavorName()
    {
        return [
            'location'    => self::QUERY,
            'description' => 'Return images which match a certain name.',
        ];
    }

    public function filterChangesSince($type)
    {
        return [
            'location'    => self::QUERY,
            'sentAs'      => 'changes-since',
            'description' => sprintf(
                "Return %ss which have been changed since a certain time. This value needs to be in an ISO 8601 format.",
                $type
            ),
        ];
    }

    public function flavorServer()
    {
        return [
            'location'    => self::QUERY,
            'description' => sprintf("Return images which are associated with a server. This value needs to be in a URL format.")
        ];
    }

    public function filterStatus($type)
    {
        return [
            'location'    => self::QUERY,
            'description' => sprintf(
                "Return %ss that have a particular status, such as \"ACTIVE\".",
                $type
            )
        ];
    }

    public function flavorType()
    {
        return [
            'location'    => self::QUERY,
            'description' => 'Return images that are of a particular type, such as "snapshot" or "backup".',
        ];
    }

    public function key()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::URL,
            'required'    => true,
            'description' => 'The specific metadata key you are interacting with',
        ];
    }

    public function ipv4()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'accessIPv4',
            'description' => 'The IP address (version 4) of the remote resource',
        ];
    }

    public function ipv6()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'accessIPv6',
            'description' => 'The IP address (version 6) of the remote resource',
        ];
    }

    public function imageId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'required'    => true,
            'sentAs'      => 'imageRef',
            'description' => 'The unique ID of the image that this server will be based on',
        ];
    }

    public function flavorId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'required'    => true,
            'sentAs'      => 'flavorRef',
            'description' => 'The unique ID of the flavor that this server will be based on',
        ];
    }

    public function metadata()
    {
        return [
            'type'        => self::OBJECT_TYPE,
            'location'    => self::JSON,
            'required'    => true,
            'description' => 'An arbitrary key/value pairing that will be used for metadata.',
            'properties'  => [
                'type'        => self::STRING_TYPE,
                'description' => <<<TYPEOTHER
The value being set for your key. Bear in mind that "key" is just an example, you can name it anything.
TYPEOTHER
            ]
        ];
    }

    public function personality()
    {
        return [
            'type'        => self::ARRAY_TYPE,
            'items'       => [
                'type'       => self::OBJECT_TYPE,
                'properties' => [
                    'path'     => [
                        'type'        => self::STRING_TYPE,
                        'description' => 'The path, on the filesystem, where the personality file will be placed'
                    ],
                    'contents' => [
                        'type'        => self::STRING_TYPE,
                        'description' => 'Base64-encoded content of the personality file'
                    ],
                ]
            ],
            'description' => <<<EOL
File path and contents (text only) to inject into the server at launch. The maximum size of the file path data is 255
bytes. The maximum limit refers to the number of bytes in the decoded data and not the number of characters in the
encoded data.
EOL

        ];
    }

    public function securityGroups()
    {
        return [
            'type'        => self::ARRAY_TYPE,
            'sentAs'      => 'security_groups',
            'description' => 'A list of security group objects which this server will be associated with',
            'items'       => [
                'type'       => self::OBJECT_TYPE,
                'properties' => ['name' => $this->name('security group')]
            ],
        ];
    }

    public function userData()
    {
        return [
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'user_data',
            'description' => 'Configuration information or scripts to use upon launch. Must be Base64 encoded.',
        ];
    }

    public function availabilityZone()
    {
        return [
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'availability_zone',
            'description' => 'The availability zone in which to launch the server.',
        ];
    }

    public function networks()
    {
        return [
            'type'        => self::ARRAY_TYPE,
            'description' => <<<EOT
A list of network objects which this server will be associated with. By default, the server instance is provisioned
with all isolated networks for the tenant. Optionally, you can create one or more NICs on the server.

To provision the server instance with a NIC for a network, specify the UUID of the network in the uuid attribute in a
networks object.

To provision the server instance with a NIC for an already existing port, specify the port-id in the port attribute in
a networks object.
EOT
            ,
            'items'       => [
                'type'       => self::OBJECT_TYPE,
                'properties' => [
                    'uuid' => [
                        'type'        => self::STRING_TYPE,
                        'description' => <<<EOL
To provision the server instance with a NIC for a network, specify the UUID of the network in the uuid attribute in a
networks object. Required if you omit the port attribute
EOL
                    ],
                    'port' => [
                        'type'        => self::STRING_TYPE,
                        'description' => <<<EOL
To provision the server instance with a NIC for an already existing port, specify the port-id in the port attribute in
a networks object. The port status must be DOWN. Required if you omit the uuid attribute.
EOL
                    ],
                ]
            ]
        ];
    }

    public function blockDeviceMapping()
    {
        return [
            'type'        => self::ARRAY_TYPE,
            'sentAs'      => 'block_device_mapping_v2',
            'description' => <<<EOL
Enables booting the server from a volume when additional parameters are given. If specified, the volume status must be
available, and the volume attach_status in OpenStack Block Storage DB must be detached.
EOL
            ,
            'items'       => [
                'type'       => self::OBJECT_TYPE,
                'properties' => [
                    'configDrive'         => [
                        'type'        => self::BOOL_TYPE,
                        'sentAs'      => 'config_drive',
                        'description' => 'Enables metadata injection in a server through a configuration drive. To enable a configuration drive, specify true. Otherwise, specify false.',
                    ],
                    'bootIndex'           => [
                        'type'        => self::INT_TYPE,
                        'sentAs'      => 'boot_index',
                        'description' => 'Indicates a number designating the boot order of the device. Use -1 for the boot volume, choose 0 for an attached volume.',
                    ],
                    'deleteOnTermination' => [
                        'type'        => self::BOOL_TYPE,
                        'sentAs'      => 'delete_on_termination',
                        'description' => 'To delete the boot volume when the server stops, specify true. Otherwise, specify false.',
                    ],
                    'guestFormat'         => [
                        'type'        => self::STRING_TYPE,
                        'sentAs'      => 'guest_format',
                        'description' => 'Specifies the guest server disk file system format, such as "ephemeral" or "swap".',
                    ],
                    'destinationType'     => [
                        'type'        => self::STRING_TYPE,
                        'sentAs'      => 'destination_type',
                        'description' => 'Describes where the volume comes from. Choices are "local" or "volume". When using "volume" the volume ID',
                    ],
                    'sourceType'          => [
                        'type'        => self::STRING_TYPE,
                        'sentAs'      => 'source_type',
                        'description' => 'Describes the volume source type for the volume. Choices are "blank", "snapshot", "volume", or "image".',
                    ],
                    'deviceName'          => [
                        'type'        => self::STRING_TYPE,
                        'sentAs'      => 'device_name',
                        'description' => 'Describes a path to the device for the volume you want to use to boot the server.',
                    ],
                ]
            ],
        ];
    }

    public function filterHost()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::QUERY,
            'description' => '',
        ];
    }

    public function filterName()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::QUERY,
            'description' => '',
        ];
    }

    public function filterFlavor()
    {
        return [
            'sentAs'      => 'flavor',
            'type'        => self::STRING_TYPE,
            'location'    => self::QUERY,
            'description' => '',
        ];
    }

    public function filterImage()
    {
        return [
            'sentAs'      => 'image',
            'type'        => self::STRING_TYPE,
            'location'    => self::QUERY,
            'description' => '',
        ];
    }

    public function password()
    {
        return [
            'sentAs'      => 'adminPass',
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'required'    => true,
            'description' => '',
        ];
    }

    public function rebootType()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'required'    => true,
            'description' => '',
        ];
    }

    public function nullAction()
    {
        return [
            'type'     => self::NULL_TYPE,
            'location' => self::JSON,
            'required' => true
        ];
    }

    public function networkLabel()
    {
        return [
            'type'     => self::STRING_TYPE,
            'location' => self::URL,
            'required' => true,
        ];
    }
}
