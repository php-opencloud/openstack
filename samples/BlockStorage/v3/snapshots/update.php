<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => ['id' => '{userId}', 'password' => '{password}'],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$service = $openstack->blockStorageV3(['catalogName' => 'cinder', 'catalogType' => 'block-storage']);

$snapshot = $service->getSnapshot('{snapshotId}');

$snapshot->name = '{newName}';
$snapshot->description = '{newDescription}';

$snapshot->update();
