<?php

namespace OpenStack\Compute\v2\Api;

class CommonParams
{
    public static $id = [
        'type'     => 'string',
        'required' => true,
        'location' => 'url'
    ];

    public static $key = [
        'type' => 'string',
        'location' => 'url',
        'required' => true,
    ];
}