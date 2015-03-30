<?php

require __DIR__ . '/../../../vendor/autoload.php';

use OpenStack\OpenStack;

$openstack = new OpenStack;

$compute = $openstack->computeV2([
    'region' => 'RegionOne',
    'debug'  => true,
]);

$s = $compute->createServer('php_test', 'e37365c2-5c45-4b73-b4ae-828436d5c569', '1');

var_dump($s);