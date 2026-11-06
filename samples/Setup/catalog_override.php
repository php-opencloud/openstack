<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user' => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
    'catalog_overrides' => [
        [
            'name'      => 'swift',
            'type'      => 'object-store',
            'region'    => '{region}',
            'interface' => 'public',
            'url'       => '{serviceUrl}',
        ],
    ],
]);
