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
    'limit' => '{limit}',
    'marker' => '{marker}',
    'sortKey' => '{sortKey}'
];

$binaries = $sahara->listJobBinaries($options);
foreach ($binaries as $binary) {
    print_r($binary);
}
