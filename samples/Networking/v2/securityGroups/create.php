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

$networking = $openstack->networkingV2();

/** @var \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup $secGroup */
$secGroup = $networking->createSecurityGroup([
    'name'        => 'new-webservers',
    'description' => 'security group for webservers',
]);
