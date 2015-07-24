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
    const BOOL_TYPE = "bool";
    const OBJECT_TYPE = "object";
    const ARRAY_TYPE = "array";
    const NULL_TYPE = "NULL";

    protected $limit = [
        'type'        => 'integer',
        'location'    => 'query',
        'description' => <<<DESC
This will limit the total amount of elements returned in a list up to the number specified. For example, specifying a
limit of 10 will return 10 elements, regardless of the actual count.
DESC
    ];

    protected $marker = [
        'type'        => 'string',
        'location'    => 'query',
        'description' => <<<DESC
Specifying a marker will begin the list from the value specified. Elements will have a particular attribute that
identifies them, such as a name or ID. The marker value will search for an element whose identifying attribute matches
the marker value, and begin the list from there.
DESC
    ];
}