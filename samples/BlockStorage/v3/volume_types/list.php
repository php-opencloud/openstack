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

$volumeTypes = $service->listVolumeTypes();

foreach ($volumeTypes as $volumeType) {
    /** @var \OpenStack\BlockStorage\v2\Models\VolumeType $volumeType */
}
