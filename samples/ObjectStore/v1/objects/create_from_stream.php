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

// You can use any instance of \Psr\Http\Message\StreamInterface
$stream = new \GuzzleHttp\Psr7\Stream(fopen('/path/to/object.txt', 'r'));

$service = $openstack->objectStoreV1();
$container = $service->getContainer('{containerName}');

$object = $container->createObject([
    'name'   => '{objectName}',
    'stream' => $stream,
]);

