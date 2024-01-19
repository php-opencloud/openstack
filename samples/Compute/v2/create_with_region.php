<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$identity = $openstack->computeV2(['region' => '{region}']);