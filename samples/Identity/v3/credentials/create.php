<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$identity = $openstack->identityV3();

$credential = $identity->createCredential([
    'blob'      => '{blob}',
    'projectId' => '{projectId}',
    'type'      => '{type}',
    'userId'    => '{userId}'
]);
