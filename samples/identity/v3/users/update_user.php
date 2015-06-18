<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$user = $identity->getUser('{userId}');

$user->description = 'foo';
$user->defaultProjectId = 'bar';
$user->name = 'baz';

$user->update();