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

$endpoint = $identity->createEndpoint([
    'interface' => \OpenStack\Identity\v3\Enum::INTERFACE_INTERNAL,
    'name'      => '{endpointName}',
    'region'    => '{region}',
    'url'       => '{endpointUrl}',
    'serviceId' => '{serviceId}'
]);
