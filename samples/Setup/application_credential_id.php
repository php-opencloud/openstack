<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl'                => '{authUrl}',
    'region'                 => '{region}',
    'application_credential' => [
        'id'     => '{applicationCredentialId}',
        'secret' => '{secret}',
    ],
]);