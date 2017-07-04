<?php declare(strict_types=1);

namespace OpenStack\Metric\v1\Gnocchi;

use OpenStack\Common\Api\AbstractParams;

class Params extends AbstractParams
{
    public function resourceType(): array
    {
        return [
            'location'    => self::URL,
            'type'        => self::STRING_TYPE,
            'description' => 'Resource type',
            'required'    => true,
        ];
    }

    public function sort(): array
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Sort criteria',
        ];
    }

    public function criteria(): array
    {
        return [
            'location'    => self::RAW,
            'type'        => self::STRING_TYPE,
            'description' => 'Filter resources based on attributes values. See http://gnocchi.xyz/stable_4.0/rest.html#searching-for-resources',
        ];
    }

    public function headerContentType(): array
    {
        return [
            'location'    => self::HEADER,
            'type'        => self::STRING_TYPE,
            'sentAs'      => 'Content-Type',
            'description' => 'Override request header',
            'documented'  => false,
        ];
    }

    public function idUrl($type)
    {
        return [
            'required'    => true,
            'location'    => self::URL,
            'description' => sprintf('The unique ID, or identifier, for the %s', $type),
        ];
    }

    public function granularity()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE,
            'description' => 'Specify the granularity to retrieve, rather than all the granularities available',
        ];
    }

    public function aggregation()
    {
        return [
            'location'    => self::QUERY,
            'type'        => self::STRING_TYPE
        ];
    }
}
