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

$project->name = 'foo';
$project->description = 'bar';
$project->enabled = false;

$project->update();