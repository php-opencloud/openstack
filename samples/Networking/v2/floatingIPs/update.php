<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
    'scope' => ['project' => ['id' => '{projectId}']]
]);

$floatingIp = $openstack->networkingV2ExtLayer3()
                        ->getFloatingIp('{id}');

$floatingIp->portId = '{newPortId}';
$floatingIp->update();
