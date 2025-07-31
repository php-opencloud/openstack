<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => [
        'id'       => '{userId}',
        'password' => '{password}'
    ],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$compute = $openstack->computeV2(['region' => '{region}']);

$server = $compute->getServer(['id' => '{serverId}']);

$securityGroups = $server->listSecurityGroups();

foreach ($securityGroups as $securityGroup) {
    /** @var \OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup $securityGroup */
}
