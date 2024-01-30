<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}',
    ],
]);

$compute = $openstack->computeV2();

$flavor = $compute->createFlavor([
    'name'  => '{flavorName}',
    'ram'   => 128,
    'vcpus' => 1,
    'swap'  => 0,
    'disk'  => 1,
]);
