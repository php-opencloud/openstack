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

$clusterTemplate = $sahara->getClusterTemplate(['id' => '{clusterTemplateId}']);
$clusterTemplate->name = '{newName}';
$clusterTemplate->isPublic = '{trueOrFalse}';
$clusterTemplate->pluginName = '{newPluginName}';
$clusterTemplate->hadoopVersion = '{newHadoopVersion}';
$clusterTemplate->update();
print_r($clusterTemplate);
