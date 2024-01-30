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

$compute = $openstack->computeV2();

$options = [
    // Required
    'name'     => '{serverName}',
    'imageId'  => '{imageId}',
    'flavorId' => '{flavorId}',

    // Required if multiple network is defined
    'networks'  => [
        ['uuid' => '{networkId}']
    ],

    // Optional
    'metadata' => ['foo' => 'bar'],
    'userData' => base64_encode('echo "Hello World. The time is now $(date -R)!" | tee /root/output.txt')
];

// Create the server
$server = $compute->createServer($options);
