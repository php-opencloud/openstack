<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}']
    ,
]);

$service = $openstack->blockStorageV3();

$volumeType = $service->getVolumeType('{volumeTypeId}');
$volumeType->delete();
