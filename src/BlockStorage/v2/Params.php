<?php
namespace OpenStack\BlockStorage\v2;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function availabilityZone()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'availability_zone',
            'description' => 'The availability zone where the entity will reside.',
        ];
    }

    public function sourceVolId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'source_volid',
            'description' => 'To create a volume from an existing volume, specify the ID of the existing volume. The ' .
                'volume is created with the same size as the source volume.',
        ];
    }

    public function desc()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'description' => 'A human-friendly description that describes the resource',
        ];
    }

    public function snapshotId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'snapshot_id',
            'description' => 'To create a volume from an existing snapshot, specify the ID of the existing volume ' .
                'snapshot. The volume is created in same availability zone and with same size as the snapshot.',
        ];
    }

    public function size()
    {
        return [
            'type'        => self::INT_TYPE,
            'location'    => self::JSON,
            'required'    => true,
            'description' => 'The size of the volume, in gibibytes (GiB).',
        ];
    }

    public function imageRef()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'imageRef',
            'description' => 'The ID of the image from which you want to create the volume. Required to create a ' .
                'bootable volume.',
        ];
    }

    public function volumeType()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'sentAs'      => 'volume_type',
            'description' => 'The associated volume type.',
        ];
    }

    public function metadata()
    {
        return [
            'type'        => self::OBJECT_TYPE,
            'location'    => self::JSON,
            'description' => 'One or more metadata key and value pairs to associate with the volume.',
            'properties'  => [
                'type'        => self::STRING_TYPE,
                'description' => <<<TYPEOTHER
The value being set for your key. Bear in mind that "key" is just an example, you can name it anything.
TYPEOTHER
            ]
        ];
    }

    public function sort()
    {
        return [
            'type'     => self::STRING_TYPE,
            'location' => self::QUERY,
            'description' => "Comma-separated list of sort keys and optional sort directions in the form of " .
                "<key>[:<direction>]. A valid direction is asc (ascending) or desc (descending)."
        ];
    }

    public function name($type)
    {
        return parent::name($type) + [
            'type'     => self::STRING_TYPE,
            'location' => self::JSON,
        ];
    }

    public function idPath()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::URL,
            'description' => 'The UUID of the resource',
        ];
    }

    public function typeSpecs()
    {
        return [
            'type' => self::OBJECT_TYPE,
            'location' => self::JSON,
            'description' => 'A key and value pair that contains additional specifications that are associated with ' .
                'the volume type. Examples include capabilities, capacity, compression, and so on, depending on the ' .
                'storage driver in use.',
        ];
    }

    public function volId()
    {
        return [
            'type'        => self::STRING_TYPE,
            'location'    => self::JSON,
            'required'    => true,
            'sentAs'      => 'volume_id',
            'description' => 'To create a snapshot from an existing volume, specify the ID of the existing volume.',
        ];
    }

    public function force()
    {
        return [
            'type'        => self::BOOL_TYPE,
            'location'    => self::JSON,
            'description' => 'Indicate whether to snapshot, even if the volume is attached. Default is false.'
        ];
    }

    public function snapshotName()
    {
        return parent::name('snapshot') + [
            'type'     => self::STRING_TYPE,
            'location' => self::JSON,
        ];
    }

    public function sortDir()
    {
        return [
            'type'     => self::STRING_TYPE,
            'location' => self::QUERY,
            'description' => "Sorts by one or more sets of attribute and sort direction combinations. If you omit " .
                "the sort direction in a set, default is desc."
        ];
    }

    public function sortKey()
    {
        return [
            'type'     => self::STRING_TYPE,
            'location' => self::QUERY,
            'description' => "Sorts by one or more sets of attribute and sort direction combinations. If you omit " .
                "the sort direction in a set, default is desc."
        ];
    }
}
