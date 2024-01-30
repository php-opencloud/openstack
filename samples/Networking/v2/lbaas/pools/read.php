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

$read = $openstack->networkingV2();

// Get the pool
$pool = $read->getLoadBalancerPool('{poolId}');
$pool->retrieve();
