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
    'pluginName'    => '{pluginName}',
    'hadoopVersion' => '{hadoopVersion}',
    'nodeGroups'    => [[
        'name'                => '{nodeGroupName}',
        'count'               => '{count}',
        'nodeGroupTemplateId' => '{nodeGroupTemplateId}',
        ],
        [
        'name'                => '{nodeGroupName}',
        'count'               => '{count}',
        'nodeGroupTemplateId' => '{nodeGroupTemplateId}',
        ],
        [
        'name'                => '{NodeGroupname}',
        'count'               => '{count}',
        'nodeGroupTemplateId' => '{nodeGroupTemplateId}',
        ],
        ],
    'name'         => '{ClusterTemplateName}',
];

$clusterTemplate = $sahara->createClusterTemplate($options);
print_r($clusterTemplate);
