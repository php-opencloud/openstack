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

$filename = '{filename}';
$jobBinaryInternal = $sahara->getJobBinaryInternal(['name' => $filename]);
$stream = \GuzzleHttp\Psr7\stream_for(fopen($filename, 'r'));

$jobBinaryInternal = $sahara->createJobBinaryInternal($stream);
print_r($jobBinaryInternal);
