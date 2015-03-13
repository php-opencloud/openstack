<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Compute\v2\Api\Server as ServerApi;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsCreatableInterface;
use OpenStack\Common\Resource\IsDeletableInterface;
use OpenStack\Common\Resource\IsUpdateableInterface;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\ResourceInterface;

class Server extends OperatorResource implements ResourceInterface,
    IsCreatableInterface,
    IsUpdateableInterface,
    IsDeletableInterface
{
    public $id;
    public $ipv4;
    public $ipv6;
    public $addresses;
    public $created;
    public $updated;
    public $flavor;
    public $hostId;
    public $image;
    public $links;
    public $metadata;
    public $name;
    public $progress;
    public $status;
    public $tenantId;
    public $userId;

    protected $aliases = [
        'block_device_mapping_v2' => 'blockDeviceMapping',
        'accessIPv4' => 'ipv4',
        'accessIPv6' => 'ipv6',
        'tenant_id'  => 'tenantId',
        'user_id'    => 'userId',
    ];

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->created = new \DateTimeImmutable($this->created);
        $this->updated = new \DateTimeImmutable($this->updated);
        //$this->flavor  = new Flavor();
        //$this->image   = new Image();
    }

    public function create(array $userOptions)
    {
        $response = $this->execute(ServerApi::post(), $userOptions);

        return $this->fromResponse($response);
    }

    public function update()
    {
        $response = $this->execute(ServerApi::put(), $this->getAttrs(['id', 'ipv4', 'ipv6']));

        $this->fromResponse($response);
    }

    public function delete()
    {
        $this->execute(ServerApi::delete(), $this->getAttrs(['id']));
    }
}