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

$domain = $identity->getDomain('{domainId}');

$domain->revokeGroupRole([
    'groupId' => '{groupId}',
    'roleId'  => '{roleId}',
]);
