<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use OpenStack\Common\Transport\Utils as TransportUtils;
use OpenStack\OpenStack;

$authUrl = 'https://keystone.example.com:5000/v2.0';

$options = [
    'authUrl'         => $authUrl,
    'region'          => 'RegionOne',
    'username'        => 'foo',
    'password'        => 'bar',
    'tenantName'      => 'baz',
    'identityService' => new Client(
        [
            'base_uri' => TransportUtils::normalizeUrl($authUrl),
            'handler'  => HandlerStack::create(),
        ]
    ),
];

/** @var OpenStack $openstack */
$openstack = new OpenStack($options);

/** @var \OpenStack\Compute\v2\Models\Server[] $servers */
$servers = $openstack->computeV2()->listServers();


