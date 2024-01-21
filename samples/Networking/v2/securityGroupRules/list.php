<?php

use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroupRule;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Service;

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
    'scope' => ['project' => ['id' => '{projectId}']]
]);

$networking = $openstack->networkingV2();

//List all security group rules
foreach ($networking->listSecurityGroupRules() as $rule) {
    /** @var SecurityGroupRule $rule */
}

/** @var SecurityGroup $securityGroup */
$securityGroup = $networking->getSecurityGroup('{uuid}');

//List rules belong to a security group
foreach ($securityGroup->securityGroupRules as $rule) {
    /**@var SecurityGroupRule $rule */
}
