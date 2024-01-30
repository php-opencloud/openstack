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

$service = $openstack->blockStorageV3();

$volumes = $service->listVolumes();

foreach ($volumes as $volume) {
    /** @var \OpenStack\BlockStorage\v2\Models\Volume $volume */
}
