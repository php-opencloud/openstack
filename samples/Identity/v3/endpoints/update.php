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

$endpoint = $identity->getEndpoint('{endpointId}');

$endpoint->interface = \OpenStack\Identity\v3\Enum::INTERFACE_PUBLIC;

$endpoint->update();
