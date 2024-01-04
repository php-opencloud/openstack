<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => ['id' => '{userId}', 'password' => '{password}'],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$service = $openstack->blockStorageV3();

$volumeTypes = $service->listVolumeTypes();

foreach ($volumeTypes as $volumeType) {
    /** @var $volumeType \OpenStack\BlockStorage\v2\VolumeType */
}
