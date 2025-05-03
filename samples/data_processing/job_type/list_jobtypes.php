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
      'plugin'  => '{pluginName}',
      'version' => '{version}',
      'hints'   => '{trueOrFalse}',
];

$jobtypes = $sahara->listJobTypes($options);

foreach ($jobtypes as $jobtype) {
    print_r($jobtype);
}
