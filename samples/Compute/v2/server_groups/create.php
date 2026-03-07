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

$compute = $openstack->computeV2(['region' => '{region}', 'microVersion' => '2.64']);

$serverGroup = $compute->createServerGroup([
    'name'   => '{serverGroupName}',
    'policy' => 'anti-affinity',
    'rules'  => ['max_server_per_host' => 3],
]);
