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

$domain = $identity->getDomain('{domainId}');

foreach ($domain->listGroupRoles(['groupId' => '{groupId}']) as $role) {
    /** @var $role \OpenStack\Identity\v3\Models\Role */
}
