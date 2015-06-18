<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$user = $identity->createUser([
    'defaultProjectId' => 'bar',
    'description'      => "Jim Doe's user",
    'domainId'         => 'foo',
    'email'            => 'baz',
    'enabled'          => true,
    'name'             => 'James Doe',
    'password'         => 'secret'
]);