<?php

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

$rules = $openstack->networkingV2ExtSecGroups()
    ->listSecurityGroupRules();

foreach ($rules as $rule) {
    /** @var \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroupRule $rule */
}
