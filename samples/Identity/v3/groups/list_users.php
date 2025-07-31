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

$group = $identity->getGroup('{groupId}');

foreach ($group->listUsers() as $user) {
    /** @var $user \OpenStack\Identity\v3\Models\User */
}
