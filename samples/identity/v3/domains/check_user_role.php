<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$identity = $openstack->identityV3(['region' => '{region}']);

$domain = $identity->getDomain('{domainId}');

if (true === $domain->checkUserRole(['userId' => '{userId}', 'roleId' => '{roleId}'])) {

}