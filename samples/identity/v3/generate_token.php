<?php

require __DIR__ . '/../../../vendor/autoload.php';

use OpenStack\OpenStack;

$client = new OpenStack;

$identity = $client->identityV3();

$token = $identity->generateToken([
    'user' => [
        'name' => '',
        'password' => '',
        'domain' => [
            'name' => '',
        ]
    ],
    'scope' => [
        'project' => [
            'id' => ''
        ]
    ]
]);