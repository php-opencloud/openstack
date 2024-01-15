<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => ['id' => '{userId}', 'password' => '{password}'],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$networking = $openstack->networkingV2();

$ports = $networking->createPorts([
    [
        'name'         => '{name1}',
        'networkId'    => '{networkId1}',
        'adminStateUp' => true
    ],
    [
        'name'         => '{name2}',
        'networkId'    => '{networkId2}',
        'adminStateUp' => true
    ],
]);
