<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
]);

$identity = $openstack->identityV3();

foreach ($identity->listDomains() as $domain) {
    /** @var $domain \OpenStack\Identity\v3\Models\Domain */
}
