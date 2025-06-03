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
    'limit'  => '{limit}',
    'marker' => '{marker}',
    'sortBy' => '{sortKey}',
];
$jobExecutions = $sahara->listJobExecutions($options);

foreach ($jobExecutions as $jobExecution) {
    print_r($jobExecution);
}
