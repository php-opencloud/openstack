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

$service = $openstack->blockStorageV3(['catalogName' => 'cinder', 'catalogType' => 'block-storage']);

$volumeType = $service->getVolumeType('{volumeTypeId}');
$volumeType->name = '{newName}';
$volumeType->update();
