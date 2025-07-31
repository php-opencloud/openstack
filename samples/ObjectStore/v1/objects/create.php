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

$service = $openstack->objectStoreV1();
$container = $service->getContainer('{containerName}');
$object = $container->createObject([
    'name'    => '{objectName}',
    'content' => '{objectContent}',
]);
