<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$compute = $openstack->computeV2(['region' => '{region}']);

$data = [
    'name'      => 'created_by_api',
    'publicKey' => 'ssh-rsa AAAAB3NAAAAB3NAAAAB3NAAAAB3NAAAAB3NAAAAB3NAAAAB3NAAAAB3NAAAAB3NAAAAB3N'
];

$keypair = $compute->createKeypair($data);

$keypair->delete();
