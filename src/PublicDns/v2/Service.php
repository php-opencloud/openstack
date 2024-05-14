<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2;

use Generator;
use OpenStack\Common\Service\AbstractService;
use OpenStack\Compute\v2\Api;
use OpenStack\PublicDns\v2\Models\DnsZone;

/**
 * Public DNS v2 service for OpenStack.
 *
 * @property \OpenStack\PublicDns\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * List servers.
     *
     * @param array         $options  {@see \OpenStack\PublicDns\v2\Api::getDnsZones}
     * @param callable|null $mapFn    a callable function that will be invoked on every iteration of the list
     *
     * @return Generator<mixed, DnsZone>
     */
    public function listDnsZone(array $options = [], callable $mapFn = null): Generator
    {
        return $this->model(DnsZone::class)->enumerate($this->api->getDnsZones(), $options, $mapFn);
    }
}
