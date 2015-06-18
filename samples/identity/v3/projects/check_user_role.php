<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$project = $identity->getProject('{projectId}');

if (true === $project->checkUserRole(['userId' => '{userId}', 'roleId' => '{roleId}'])) {

}