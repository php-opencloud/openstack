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

$service = $openstack->networkingV2();
$networks = $service->createNetworks([
    [
        'name' => '{networkName1}',
    ],
    [
        'name' => '{networkName2}',
    ],
]);
