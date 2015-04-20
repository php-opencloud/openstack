<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
'username' => '{username}',
'password' => '{password}',
'tenantId' => '{tenantId}',
'authUrl'  => '{authUrl}',
]);

$compute = $openstack->computeV2(['region' => '{region}']);

$server = $compute->getServer(['id' => '{serverId}']);

// By default, this will return an empty Server object and NOT hit the API.
// This is convenient for when you want to use the object for operations
// that do not require an initial GET request. To retrieve the server's details,
// run the following, which *will* call the API with a GET request:

$server->retrieve();