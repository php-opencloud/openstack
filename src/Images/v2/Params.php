<?php

namespace OpenStack\Images\v2;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function imageName()
    {
        return array_merge($this->name('image'), [
            'description' => 'Name for the image. The name of an image is not unique to an Image service node. The ' .
                             'API cannot expect users to know the names of images owned by others.',
            'required'    => true,
        ]);
    }

    public function visibility()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::STRING_TYPE,
            'description' => 'Image visibility. Public or private. Default is public.',
            'enum'        => ['private', 'public']
        ];
    }

    public function tags()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::ARRAY_TYPE,
            'description' => 'Image tags',
            'items'       => ['type' => self::STRING_TYPE]
        ];
    }

    public function containerFormat()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'container_format',
            'description' => 'Format of the container. A valid value is ami, ari, aki, bare, ovf, or ova.',
            'enum'        => ['ami', 'ari', 'aki', 'bare', 'ovf', 'ova'],
        ];
    }

    public function diskFormat()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'disk_format',
            'description' => 'Format of the container. A valid value is ami, ari, aki, bare, ovf, or ova.',
            'enum'        => ['ami', 'ari', 'aki', 'vhd', 'vmdk', 'raw', 'qcow2', 'vdi', 'iso'],
        ];
    }

    public function minDisk()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::INT_TYPE,
            'sentAs'      => 'min_disk',
            'description' => 'Amount of disk space in GB that is required to boot the image.',
        ];
    }

    public function minRam()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::INT_TYPE,
            'sentAs'      => 'min_ram',
            'description' => 'Amount of RAM in GB that is required to boot the image.',
        ];
    }

    public function protectedParam()
    {
        return [
            'location'    => self::JSON,
            'type'        => self::BOOL_TYPE,
            'description' => 'If true, image is not deletable.',
        ];
    }

    public function queryName()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Shows only images with this name. A valid value is the name of the image as a string.',
        ];
    }

    public function queryVisibility()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Shows only images with this image visibility value or values.',
            'enum'        => ['public', 'private', 'shared'],
        ];
    }

    public function queryMemberStatus()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Shows only images with this member status.',
            'enum'        => ['accepted', 'pending', 'rejected', 'all'],
        ];
    }

    public function queryOwner()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Shows only images that are shared with this owner.',
        ];
    }

    public function queryStatus()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Shows only images with this image status.',
            'enum'        => ['queued', 'saving', 'active', 'killed', 'deleted', 'pending_delete'],
        ];
    }

    public function querySizeMin()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::INT_TYPE,
            'description' => 'Shows only images with this minimum image size.',
        ];
    }

    public function querySizeMax()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::INT_TYPE,
            'description' => 'Shows only images with this maximum image size.',
        ];
    }

    public function queryTag()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Image tag.',
        ];
    }
}