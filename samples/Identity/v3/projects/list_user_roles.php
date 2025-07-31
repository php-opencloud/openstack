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

$project = $identity->getProject('{id}');

foreach ($project->listUserRoles(['userId' => '{projectUserId}']) as $role) {
    /** @var $role \OpenStack\Identity\v3\Models\Role */
}
