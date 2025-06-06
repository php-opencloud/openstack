<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => '{authUrl}',
    'region'  => '{region}',
    'user'    => ['id' => '{userId}', 'password' => '{password}'],
    'scope'   => ['project' => ['id' => '{projectId}']]
]);

$service = $openstack->blockStorageV3(['catalogName' => 'cinder', 'catalogType' => 'block-storage']);

$snapshots = $service->listSnapshots(true);

foreach ($snapshots as $snapshot) {
    /** @var $snapshot \OpenStack\BlockStorage\v2\Models\Snapshot */
}
