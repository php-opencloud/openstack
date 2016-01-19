<?php

require __DIR__ . '/../../../vendor/autoload.php';

use OpenStack\OpenStack;

$client = new OpenStack;

$objectStore = $client->objectStoreV2([
    'region' => 'RegionOne',
    //'debug' => true,
]);
