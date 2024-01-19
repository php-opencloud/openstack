<?php

require 'vendor/autoload.php';

use OpenStack\Images\v2\Models\Member;

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}',
    ],
]);

$service = $openstack->imagesV2();

$image = $service->getImage('{imageId}');
$member = $image->getMember('{projectId}');
$member->updateStatus(Member::STATUS_ACCEPTED);
