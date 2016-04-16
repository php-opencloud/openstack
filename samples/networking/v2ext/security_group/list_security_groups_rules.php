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

//List rules belong to a security group
$securityGroup = $networkingExtSecGroup->getSecurityGroup(['id' => '{uuid}']);
foreach($securityGroup->securityGroupRules as $rule)
{

}


//All rules
foreach($networkingExtSecGroup->listSecurityGroupRules() as $group)
{

}
