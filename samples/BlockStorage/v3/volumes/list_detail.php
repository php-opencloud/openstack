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

foreach ($service->listVolumes(true) as $volume) {
    /** @var $volume \OpenStack\BlockStorage\v2\Models\Volume */
}
