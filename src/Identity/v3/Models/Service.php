<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Service extends AbstractResource implements Creatable, Listable, Retrievable, Updateable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var string */
    public $description;

    /** @var []Endpoint */
    public $endpoints;

    /** @var array */
    public $links;

    protected $resourceKey = 'service';
    protected $resourcesKey = 'services';

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postServices}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postServices(), $data);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getService());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->patchService());
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteService());
    }

    /**
     * Retrieve the base URL for a service.
     *
     * @param string $name   The name of the service as it appears in the catalog.
     * @param string $type   The type of the service as it appears in the catalog.
     * @param string $region The region of the service as it appears in the catalog.
     * @param string $urlType
     *
     * @return string|false
     */
    public function getUrl($name, $type, $region, $urlType)
    {
        if (($this->name !== $name && !empty($this->name)) || $this->type !== $type) {
            return false;
        }

        foreach ($this->endpoints as $endpoint) {
            if ($endpoint->region == $region && $endpoint->interface == $urlType) {
                return $endpoint->url;
            }
        }

        return false;
    }
}
