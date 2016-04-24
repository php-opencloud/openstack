<?php

use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
    'scope' => [
        'project' => [
            'id' => '{projectId}'
        ]
    ]
]);

$networkingExtSecGroup = $openstack->networkingV2ExtSecGroups();

/** @var SecurityGroup $securityGroup */
$securityGroup = $networkingExtSecGroup->createSecurityGroup([
    'name' => 'New SecGroup',
    'description' => 'Foo Barrr'
]);


$networkingExtSecGroup->listSecurityGroups();