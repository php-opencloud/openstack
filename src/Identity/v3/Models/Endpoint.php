<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Updateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Endpoint extends AbstractResource implements Creatable, Updateable, Deletable
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
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $response = $this->executeWithState($this->api->patchEndpoint());
        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteEndpoint(), $this->getAttrs(['id']));
    }
}