<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsCreatable;
use OpenStack\Common\Resource\IsDeletable;
use OpenStack\Common\Resource\IsUpdateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Endpoint extends AbstractResource implements IsCreatable, IsUpdateable, IsDeletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $interface;

    /** @var string */
    public $name;

    /** @var string */
    public $serviceId;

    /** @var string */
    public $region;

    /** @var array */
    public $links;

    /** @var string */
    public $url;

    protected $resourceKey = 'endpoint';

    protected $aliases = ['service_id' => 'serviceId'];

    public function create(array $data)
    {
        $response = $this->execute($this->api->postEndpoints(), $data);
        $this->populateFromResponse($response);
        return $this;
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}