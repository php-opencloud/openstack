<?php

require '../../../vendor/autoload.php';


$openstack = new OpenStack\OpenStack([
    'authUrl'  => '{AUTH_URL}',
    'region'   => '{REGION}',
    'user'     => [
        'domain'   => [
            'name' => '{DOMAIN_NAME}'
        ],
        'name'     => '{USER_LOGIN}',
        'password' => '{USER_PASSWORD}',
    ],
    'scope'    => ['project' => ['id' => '{PROJECT_ID}']]
]);

$publicDns = $openstack->publicDnsV2();

$dnsList = $publicDns->listDnsZone();

foreach ($dnsList as $item) {
    var_dump($item); exit();
}
