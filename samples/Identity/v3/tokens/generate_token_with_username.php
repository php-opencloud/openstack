<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'name'     => '{username}',
        'password' => '{password}',
        'domain'   => ['id' => '{domainId}']
    ],
]);

$identity = $openstack->identityV3();

$token = $identity->generateToken([
    'user' => [
        'name'     => '{username}',
        'password' => '{password}',
        'domain'   => [
            'id' => '{domainId}'
        ]
    ]
]);
