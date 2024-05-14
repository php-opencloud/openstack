<?php

use OpenStack\PublicDns\v2\DnsRecordType;

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

$records = $publicDns->listDnsZoneRecord(['dnsUuid' => 'a2af0970-e04e-4232-8720-518ba2021a3d', 'type' => DnsRecordType::MX]);

foreach ($records as $record) {
    var_dump($record); exit();
}

//
//$pubDns = $publicDns->getDnsZone(['uuid' => 'a2af0970-e04e-4232-8720-518ba2021a3d']);
//
//$pubDns->retrieve();
//var_dump($pubDns, $pubDns->listDnsRecord(['type' => DnsRecordType::MX])); exit();
