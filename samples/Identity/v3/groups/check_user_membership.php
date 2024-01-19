<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$identity = $openstack->identityV3();

$group = $identity->getGroup('{groupId}');

$hasMembership = $group->checkMembership(['userId' => '{groupUserId}']);
