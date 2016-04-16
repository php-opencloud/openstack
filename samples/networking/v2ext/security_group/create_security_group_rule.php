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
$securityGroup = $networkingExtSecGroup->createSecurityGroupRule([
    'direction' => 'ingress', //ingress or egress
    'protocol' => 'tcp', //tcp, udp, icmp
    'securityGroupId' => '{securityGroupUUID}',
    'portRangeMin' => '123',
    'portRangeMax' => '456',
    'remoteIpPrefix' => '10.0.0.0/24', //Optional
    //'remoteGroupId' => '{remoteSecurityGroupId}'
]);
