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
use OpenStack\PublicDns\v2\Api;

/**
 * @property Api $api
 */
class DnsRecord extends OperatorResource implements Creatable, Updateable, Deletable, Retrievable, Listable
{
    use HasWaiterTrait;

    public string     $uuid;
    public string     $name;
    public string     $dns;
    public string     $ip;
    public string     $content;
    public string|int $ttl;

    protected $resourceKey  = '';
    protected $resourcesKey = '';
    protected $markerKey    = 'uuid';

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
        // TODO: Implement delete() method.
    }

    public function update()
    {
        // TODO: Implement update() method.
    }
}
