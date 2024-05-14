<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params();
    }

    public function getDnsZones(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'v2/dns/', // Последний слэш это ньанс от VK
            'params' => [
                'tenant' => $this->params->tenant(),
            ],
        ];
    }

    public function getDnsZone(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'v2/dns/{dnsUuid}',
            'params' => [
                'dnsUuid' => $this->params->dnsUuid(),
            ],
        ];
    }

    public function getDnsZoneRecords(DnsRecordType $type = DnsRecordType::A): array
    {

        return [
            'method' => 'GET',
            'path'   => 'v2/dns/{dnsUuid}/' . $type->value . '/',
            'params' => [
                'dnsUuid' => $this->params->dnsUuid(),
            ],
        ];
    }

    public function getDnsZoneRecord(DnsRecordType $type = DnsRecordType::A): array
    {
        return [
            'method' => 'GET',
            'path'   => 'v2/dns/{uuid}/' . $type->value . '/{recordUuid}',
            'params' => [
                'dnsUuid'    => $this->params->dnsUuid(),
                'recordUuid' => $this->params->recordUuid(),
            ],
        ];
    }
}
