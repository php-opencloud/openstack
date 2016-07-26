<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use OpenStack\Identity\v2\Service;
use OpenStack\Common\Transport\Utils as TransportUtils;
use OpenStack\OpenStack;

$httpClient = new Client([
    'base_uri' => TransportUtils::normalizeUrl(getenv('OS_AUTH_URL')),
    'handler'  => HandlerStack::create(),
]);
        
$options = [
    'authUrl'         => 'https://example.com:5000/v2.0',
    'region'          => 'RegionOne',
    'username'        => 'foo',
    'password'        => 'bar',
    'tenantName'      => 'baz',
    'identityService' => Service::factory($httpClient),
];

$openstack = new OpenStack($options);
