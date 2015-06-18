<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$policy = $identity->getPolicy('{policyId}');

$policy->type = 'foo';

$policy->retrieve();