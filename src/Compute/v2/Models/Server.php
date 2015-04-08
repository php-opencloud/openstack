<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\IsCreatable;
use OpenStack\Common\Resource\IsDeletable;
use OpenStack\Common\Resource\IsRetrievable;
use OpenStack\Common\Resource\IsRetrievableInterface;
use OpenStack\Common\Resource\IsUpdateable;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Compute\v2\Api;

class Server extends AbstractResource implements
    IsCreatable,
    IsUpdateable,
    IsDeletable,
    IsRetrievable
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
        $this->flavor = $this->model('Flavor', $data['flavor']);
        $this->image = $this->model('Image', $data['image']);
    }

    /**
     * @param array $userOptions
     * @return $this|IsCreatable
     */
    public function create(array $userOptions)
    {
        $response = $this->execute(Api::postServer(), $userOptions);

        return $this->populateFromResponse($response);
    }

    /**
     * @return void
     */
    public function update()
    {
        $response = $this->execute(Api::putServer(), $this->getAttrs(['id', 'ipv4', 'ipv6']));

        $this->populateFromResponse($response);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $response = $this->execute(Api::deleteServer(), $this->getAttrs(['id']));

        if ($response->getStatusCode() === 204) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    public function retrieve()
    {
        $response = $this->execute(Api::getServer());

        $this->populateFromResponse($response);
    }
}