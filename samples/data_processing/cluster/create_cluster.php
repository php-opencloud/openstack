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

$options = [
    'pluginName'               => '{pluginName}',
    'hadoopVersion'            => '{hadoopVersion}',
    'clusterTemplateId'        => '{clusterTemplateId}',
    'defaultImageId'           => '{defaultImageId}',
    //user keypair id is optional
    'userKeypairId'            => '{userKeypairId}',
    'name'                     => '{name}',
    'neutronManagementNetwork' => '{neutronManagementNetworkId}',
];

$cluster = $sahara->createCluster($options);
