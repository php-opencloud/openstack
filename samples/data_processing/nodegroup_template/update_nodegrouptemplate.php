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

$template = $sahara->getNodeGroupTemplate(['id' => '{nodeGroupTemplateId}']);
$template->name = '{newName}';
$template->isPublic = '{trueOrFalse}';
$template->isProtected = '{trueOrFalse}';
$template->update();
