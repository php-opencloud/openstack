<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

if (true === $identity->validateToken('{tokenId}')) {
    // It's valid!
}