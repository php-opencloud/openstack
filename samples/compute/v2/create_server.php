<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$compute = $openstack->computeV2(['region' => '{region}']);

$options = [
    // Required
    'name'     => '{serverName}',
    'imageId'  => '{imageId}',
    'flavorId' => '{flavorId}',

    // Optional
    'metadata' => ['foo' => 'bar'],
    'userData' => base64_encode('echo "Hello World. The time is now $(date -R)!" | tee /root/output.txt')
];

// Create the server
$server = $compute->createServer($options);