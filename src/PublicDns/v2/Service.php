<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2;

use Generator;
use OpenStack\Common\Service\AbstractService;
use OpenStack\Common\Transport\Utils;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\PublicDns\v2\Models\DnsRecord;
use OpenStack\PublicDns\v2\Models\DnsZone;

/**
 * Public DNS v2 service for OpenStack.
 *
 * @property \OpenStack\PublicDns\v2\Api $api
 */
class Service extends AbstractService
{
    public function listDnsZone(array $options = [], callable $mapFn = null): Generator
    {
        return $this->model(DnsZone::class)->enumerate($this->api->getDnsZones(), $options, $mapFn);
    }

    public function getDnsZone(array $options = []): DnsZone
    {
        $dnsZone = $this->model(DnsZone::class);
        $dnsZone->populateFromArray($options);

        return $dnsZone;
    }

    public function listDnsZoneRecord(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(DnsRecord::class)->enumerate($this->api->getDnsZoneRecords(), $options, $mapFn);
    }

    public function getDnsZoneRecord(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(DnsRecord::class)->enumerate($this->api->getDnsZoneRecord(), $options, $mapFn);
    }
}
