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

$token = $identity->generateToken([
    'application_credential' => [
        'id'     => '{applicationCredentialId}',
        'secret' => '{secret}'
    ]
]);
