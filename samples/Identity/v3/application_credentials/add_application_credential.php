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

$user = $identity->getUser('{userId}');
$applicationCredential = $user->createApplicationCredential([
    'name'        => '{name}',
    'description' => '{description}',
]);
