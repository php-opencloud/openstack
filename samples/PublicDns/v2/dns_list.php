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

//$dnsList = $publicDns->listDnsZone();
//
//foreach ($dnsList as $item) {
//    $item->retrieve();
//    var_dump($item); exit();
//}

$pubDns = $publicDns->getDnsZone(['uuid' => 'a2af0970-e04e-4232-8720-518ba2021a3d']);

var_dump($pubDns); exit();
