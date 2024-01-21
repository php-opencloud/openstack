<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}',
    ],
]);

$options = [
];

$service = $openstack->objectStoreV1();
$container = $service->getContainer('{containerName}');

$object = $container->createObject([
    'name'             => '{objectName}',
    'stream'           => new \GuzzleHttp\Psr7\Stream(fopen('/path/to/large_object.mov', 'r')),

    // optional: specify the size of each segment in bytes
    'segmentSize'      => 1073741824,

    // optional: specify the container where the segments live. This does not necessarily have to be the
    // same as the container which holds the manifest file
    'segmentContainer' => 'test_segments',
]);
