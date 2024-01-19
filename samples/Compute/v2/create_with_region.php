<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$compute = $openstack->computeV2(['region' => '{region}']);