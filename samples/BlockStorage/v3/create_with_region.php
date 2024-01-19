<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$identity = $openstack->blockStorageV3(['region' => '{region}']);