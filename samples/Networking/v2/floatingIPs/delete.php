<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}',
    ],
    'scope'   => ['project' => ['id' => '{projectId}']],
]);

$networking = $openstack->networkingV2();
$floatIp = $networking->getFloatingIp('{id}');

$floatIp->delete();
