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

$subnet = $service->createSubnet([
    'name'      => '{subnetName}',
    'networkId' => '{networkId}',
    'ipVersion' => 4,
    'cidr'      => '192.168.199.0/25',
    'gatewayIp' => '192.168.199.128',
]);
