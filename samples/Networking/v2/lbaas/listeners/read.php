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

$service = $openstack->networkingV2();
$listener = $service->getLoadBalancerListener('{listenerId}');

$listener->retrieve();
