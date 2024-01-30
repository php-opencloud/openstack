<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id' => '{userId}',
        'password' => '{password}'
    ],
]);

$service = $openstack->networkingV2();

$port = $service->createPort([
    'name'         => '{portName}',
    'networkId'    => '{networkId}',
    'adminStateUp' => true,
    'fixedIps' => [
        [
            'ipAddress' => '192.168.199.100'
        ],
        [
            'ipAddress' => '192.168.199.200'
        ]
    ]
]);
