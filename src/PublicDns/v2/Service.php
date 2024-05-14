<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2;

use Generator;
use OpenStack\Common\Service\AbstractService;
use OpenStack\Compute\v2\Api;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\PublicDns\v2\Models\DnsZone;

/**
 * Public DNS v2 service for OpenStack.
 *
 * @property \OpenStack\PublicDns\v2\Api $api
 */
class Service extends AbstractService
{
    /**
     * List public dns zones.
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

    /**
     * Retrieve a public dns zone object without calling the remote API. Any values provided in the array will populate the
     * empty object, allowing you greater control without the expense of network transactions. To call the remote API
     * and have the response populate the object, call {@see DnsZone::retrieve}. For example:.
     *
     * <code>$dnsZone = $dnsZone->getDnsZone(['uuid' => '{dnsUuid}']);</code>
     *
     * @param array $options An array of attributes that will be set on the {@see DnsZone} object. The array keys need to
     *                       correspond to the class public properties.
     */
    public function getDnsZone(array $options = []): DnsZone
    {
        $dnsZone = $this->model(DnsZone::class);
        $dnsZone->populateFromArray($options);

        return $dnsZone;
    }

}
