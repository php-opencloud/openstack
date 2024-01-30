<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$service = $openstack->imagesV2(['region' => '{region}']);