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

$compute = $openstack->computeV2(['region' => '{region}']);

$flavor = $compute->getFlavor(['id' => '{flavorId}']);
$flavor->delete();
