<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user' => [
        'id'       => '{userId}',
        'password' => '{password}'
    ]
]);