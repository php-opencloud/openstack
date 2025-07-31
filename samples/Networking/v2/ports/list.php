<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}',
    ],
]);

$service = $openstack->networkingV2();

foreach ($service->listPorts() as $port) {
    /** @var \OpenStack\Networking\v2\Models\Port $port */
}
