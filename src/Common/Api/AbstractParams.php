<?php

namespace OpenStack\Common\Api;

abstract class AbstractParams
{
    // locations
    const QUERY = 'query';
    const HEADER = 'header';
    const URL = 'url';
    const JSON = 'json';
    const RAW = 'raw';

    // types
    const STRING_TYPE = "string";
    const BOOL_TYPE = "boolean";
    const OBJECT_TYPE = "object";
    const ARRAY_TYPE = "array";
    const NULL_TYPE = "NULL";
    const INT_TYPE = 'integer';

    public function limit()
    {
        return [
            'type'        => self::INT_TYPE,
            'location'    => 'query',
            'description' => <<<DESC
This will limit the total amount of elements returned in a list up to the number specified. For example, specifying a
limit of 10 will return 10 elements, regardless of the actual count.
DESC
        ];
    }

    public function marker()
    {
        return [
            'type'        => 'string',
            'location'    => 'query',
            'description' => <<<DESC
Specifying a marker will begin the list from the value specified. Elements will have a particular attribute that
identifies them, such as a name or ID. The marker value will search for an element whose identifying attribute matches
the marker value, and begin the list from there.
DESC
        ];
    }

    public function id($type)
    {
        return [
            'description' => sprintf("The unique ID, or identifier, for the %s", $type)
        ];
    }

    public function name($resource)
    {
        return [
            'description' => sprintf("The name of the %s", $resource)
        ];
    }
}
