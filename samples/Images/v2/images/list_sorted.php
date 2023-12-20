<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => ['id' => '{userId}', 'password' => '{password}'],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$images = $openstack->imagesV2()->listImages(['sortKey' => '{sortKey}', 'sortDir' => '{sortDir}']);

foreach ($images as $image) {
    /** @var \OpenStack\Images\v2\Models\Image $image */
}
