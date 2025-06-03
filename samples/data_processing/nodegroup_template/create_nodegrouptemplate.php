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
    'nodeProcesses' => [
        'namenode',
        'datanode'
    ],
    'name'          => '{nodeGroupTemplateName}',
    'flavorId'      => '{flavorId}',
    'floatingIpPool' => '{floatingIpPool}',
    'autoSecurityGroup' => '{trueOrFalse}',
    'isProtected'   => '{trueOrFalse}',
];

$template = $sahara->createNodeGroupTemplate($options);
