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

$image  = $service->getImage('{imageId}');

$stream = function_exists('\GuzzleHttp\Psr7\stream_for')
    ? \GuzzleHttp\Psr7\stream_for(fopen('{fileName}', 'r'))
    : \GuzzleHttp\Psr7\Utils::streamFor(fopen('{fileName}', 'r'));

$image->uploadData($stream);
