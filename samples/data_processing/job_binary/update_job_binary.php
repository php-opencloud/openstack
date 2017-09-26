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

$binary = $sahara->getJobBinary(['id' => '{jobBinaryId}']);
$binary->url = '{newUrl}';
$binary->name = '{newName}';
$binary->description = '{description}';
$binary->isPublic = '{trueOrFalse}';
$binary->isProtected = '{trueOrFalse}';
$binary->update();
