<?php

declare(strict_types=1);

namespace OpenStack\PublicDns\v2\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasWaiterTrait;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;
use OpenStack\Common\Transport\Utils;
use OpenStack\PublicDns\v2\Api;

/**
 * @property Api $api
 */
class DnsZone extends OperatorResource implements Creatable, Updateable, Deletable, Retrievable, Listable
{
    use HasWaiterTrait;

    public string $uuid;
    public string $tenant;
    public string $soaPrimaryDns;
    public string $soaAdminEmail;
    public int    $soaSerial;
    public int    $soaRefresh;
    public int    $soaRetry;
    public int    $soaExpire;
    public int    $soaTtl;
    public string $zone;
    public string $status;

    protected $resourceKey  = '';
    protected $resourcesKey = '';
    protected $markerKey    = '';


    protected $aliases = [
        'soa_primary_dns' => 'soaPrimaryDns',
        'soa_admin_email' => 'soaAdminEmail',
        'soa_serial'      => 'soaSerial',
        'soa_refresh'     => 'soaRefresh',
        'soa_retry'       => 'soaRetry',
        'soa_expire'      => 'soaExpire',
        'soa_ttl'         => 'soaTtl',
    ];

    public function create(array $userOptions): Creatable
    {
        // TODO: Implement create() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function retrieve(): void
    {
        $response = $this->execute($this->api->getDnsZone(), ['dnsUuid' => $this->uuid]);
        $this->populateFromResponse($response);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function listDnsRecord(array $options = []): array
    {
        $options['dnsUuid'] = $this->uuid;

        $data     = $this->api->getDnsZoneRecords($options['type']);
        $response = $this->execute($data, $options);

        return Utils::jsonDecode($response);
    }
}
