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

$user = $identity->getUser('{id}');

foreach ($user->listProjects() as $project) {
    /** @var $project \OpenStack\Identity\v3\Models\Project */
}
