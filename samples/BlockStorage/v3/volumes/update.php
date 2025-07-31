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

$service = $openstack->blockStorageV3(['catalogName' => 'cinder', 'catalogType' => 'block-storage']);

$volume = $service->getVolume('{volumeId}');

$volume->name = '{newName}';
$volume->description = '{newDescription}';

$volume->update();
