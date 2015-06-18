<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$policy = $identity->createPolicy([
    'blob'      => 'blob',
    'projectId' => 'project_id',
    'type'      => 'ec2',
    'userId'    => 'user_id'
]);