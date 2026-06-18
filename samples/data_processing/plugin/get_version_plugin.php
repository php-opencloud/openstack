<?php

require 'vendor/autoload.php';

use OpenStack\OpenStack;

$openstack = new OpenStack([
    'authUrl' => '{authUrl}',
    'user'    => [
        'name'     => '{userName}',
        'password' => '{password}',
        'domain'   => ['name' => '{userDomain}'],
    ],
    'scope'   => [
        'project'  => [
             'name'   => '{projectName}',
             'domain' => ['name' => '{projectDomain}'],
        ],
    ],
]);

$sahara = $openstack->dataProcessingV1(['region' => '{region}']);

$dataProcessing = $sahara->getPlugin(['plugin_name' => '{name}', 'versions' => '{version}']);
$data = $dataProcessing->retrieveDetails();
$nodeProcesses = $data['node_processes'];
$configs = $data['configs'];
$requiredImageTag = $data['required_image_tags'];
print_r($configs);
print_r($nodeProcesses);
print_r($requiredImageTag);
