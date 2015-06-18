<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$endpoint = $identity->createEndpoint([
    'interface' => \OpenStack\Identity\v3\Enum::INTERFACE_INTERNAL,
    'name'      => 'endpointName',
    'region'    => 'RegionOne',
    'url'       => 'myopenstack.org:12345/v2.0',
    'serviceId' => 'serviceId'
]);