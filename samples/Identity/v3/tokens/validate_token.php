<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'name'     => '{username}',
        'password' => '{password}',
        'domain'   => ['id' => '{domainId}']
    ],
]);

$identity = $openstack->identityV3();

$result = $identity->validateToken('{tokenId}');

if (true === $result) {
    // It's valid!
}
