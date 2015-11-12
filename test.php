<?php

require 'vendor/autoload.php';

$openstack = new OpenStack\OpenStack([
    'authUrl' => 'http://104.239.168.215:5000/v3',
    'region'  => 'RegionOne',
    'user'    => [
        'name'     => 'admin',
        'password' => 'password',
        'domain'   => ['id' => 'default']
    ],
    'scope' => [
        'project' => ['id' => 'e00abf65afca49609eedd163c515cf10']
    ]
]);

$identity = $openstack->identityV3();

// Since usernames will not be unique across an entire OpenStack installation,
// when authenticating with them you must also provide your domain ID. You do
// not have to do this if you authenticate with a user ID.

$token = $identity->generateToken([
    'user' => [
        'name'     => 'admin',
        'password' => 'password',
        'domain'   => [
            'id' => 'default'
        ]
    ]
]);
