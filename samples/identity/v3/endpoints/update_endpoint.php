<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$endpoint = $identity->getEndpoint(['id' => '{endpointId}']);

$endpoint->interface = \OpenStack\Identity\v3\Enum::INTERFACE_PUBLIC;
$endpoint->region = 'foo';
$endpoint->serviceId = 'bar';

$endpoint->update();