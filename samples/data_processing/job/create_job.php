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
    'description' => '{description}',
    'mains'       => ['{main binary id}'],
    'libs'        => ['{library binary id}'],
    'type'        => '{job-type}',
    'name'        => '{job-name}',
];

$job = $sahara->createJob($options);
print_r($job);
