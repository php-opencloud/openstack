<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$token = $identity->generateToken([
    'user' => [
        'id'       => '{userId}',
        'password' => '{userPassword}'
    ],
    'scope' => [
        'domain' => ['id' => '{domainId}']
    ]
]);