<?php

$rootDir = dirname(dirname(__DIR__));

require_once $rootDir . '/vendor/autoload.php';

$basePath  = $rootDir . '/samples';

$runner = new \OpenStack\Integration\Runner($basePath);
$runner->runServices();
