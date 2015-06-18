<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

// Since usernames will not be unique across an entire OpenStack installation,
// when authenticating with them you must also provide your domain ID. You do
// not have to do this if you authenticate with a user ID.

$token = $identity->generateToken([
    'user' => [
        'name'     => '{username}',
        'password' => '{userPassword}',
        'domain'   => ['id' => '{domainId}']
    ]
]);