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

foreach ($container->listObjects() as $object) {
    /** @var \OpenStack\ObjectStore\v1\Models\StorageObject $object */
}
