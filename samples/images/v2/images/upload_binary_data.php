<?php

use GuzzleHttp\Psr7\Utils;

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => ['id' => '{userId}', 'password' => '{password}'],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$service = $openstack->imagesV2();

$image  = $service->getImage('{imageId}');
$stream = Utils::streamFor(fopen('{fileName}', 'r'));
$image->uploadData($stream);
