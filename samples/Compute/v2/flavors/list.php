<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}',
    ],
]);

$compute = $openstack->computeV2();

$flavors = $compute->listFlavors();

foreach ($flavors as $flavor) {
    /** @var \OpenStack\Compute\v2\Models\Flavor $flavor */
}
