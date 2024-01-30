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

$service = $openstack->imagesV2();

$images = $service->listImages(['sortKey' => '{sortKey}', 'sortDir' => '{sortDir}']);

foreach ($images as $image) {
    /** @var \OpenStack\Images\v2\Models\Image $image */
}
