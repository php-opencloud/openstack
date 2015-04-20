<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'username' => '{username}',
    'password' => '{password}',
    'tenantId' => '{tenantId}',
    'authUrl'  => '{authUrl}',
]);

$compute = $openstack->computeV2(['region' => '{region}']);

$flavors = $compute->listFlavors();

foreach ($flavors as $flavor) {

}