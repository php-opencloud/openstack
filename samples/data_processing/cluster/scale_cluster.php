<?php

require 'vendor/autoload.php';

use OpenStack\OpenStack;

$openstack = new OpenStack([
    'authUrl' => '{authUrl}',
    'user'    => [
        'name'     => '{userName}',
        'password' => '{password}',
        'domain'   => ['name' => '{userDomain}'],
    ],
    'scope'   => [
        'project'  => [
             'name'   => '{projectName}',
             'domain' => ['name' => '{projectDomain}'],
        ],
    ],
]);

$sahara = $openstack->dataProcessingV1(['region' => '{region}']);

$options = [
    'id'               => '{clusterId}',
    'addNodeGroups'    => [[
        'count'               => '{count}',
        'name'                => '{name}',
        'nodeGroupTemplateId' => '{nodeGroupTemplateId}',
    ]],
    'resizeNodeGroups' => [[
        'count'               => '{count}',
        'name'                => '{name}',
    ]],
];

$cluster = $sahara->scaleCluster($options);
