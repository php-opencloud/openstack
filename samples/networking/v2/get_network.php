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

// By default, this will return an empty Network object and NOT hit the API.
// This is convenient for when you want to use the object for operations
// that do not require an initial GET request. To retrieve the network's details,
// run the following, which *will* call the API with a GET request:

$network->retrieve();
