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

$floatingIp = $networking->createFloatingIp([
    "floatingNetworkId" => "{networkId}",
    "portId"            => "{portId}",
    'fixedIpAddress'    => '{fixedIpAddress}',
]);
