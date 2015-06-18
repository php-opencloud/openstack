<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

// Since project names will not be unique across an entire OpenStack installation,
// when authenticating with them you must also provide your domain ID. You do
// not have to do this if you authenticate with a project ID.

$token = $identity->generateToken([
    'user' => [
        'id'       => '{userId}',
        'password' => '{userPassword}'
    ],
    'scope' => [
        'project' => [
            'name' => '{projectName}',
            'domain' => [
                'name' => '{domainName}'
            ]
        ]
    ]
]);