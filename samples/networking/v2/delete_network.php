<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
'username' => '{username}',
'password' => '{password}',
'tenantId' => '{tenantId}',
'authUrl'  => '{authUrl}',
]);

$networking = $openstack->networkingV2(['region' => '{region}']);

$network = $networking->getNetwork(['id' => '{networkId}']);

$network->delete();