<?php declare(strict_types=1);

namespace OpenStack\Identity\v3\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Retrievable;
use OpenCloud\Common\Resource\Updateable;

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

    private function nameMatches($value)
    {
        return $this->name && $this->name == $value;
    }

    private function typeMatches($value)
    {
        return $this->type && $this->type = $value;
    }

    /**
     * Retrieve the base URL for a service.
     *
     * @param string $name      The name of the service as it appears in the catalog.
     * @param string $type      The type of the service as it appears in the catalog.
     * @param string $region    The region of the service as it appears in the catalog.
     * @param string $interface The interface of the service as it appears in the catalog.
     *
     * @return string|false
     */
    public function getUrl($name, $type, $region, $interface)
    {
        if (!$this->nameMatches($name) || !$this->typeMatches($type)) {
            return false;
        }

        foreach ($this->endpoints as $endpoint) {
            if ($endpoint->regionMatches($region) && $endpoint->interfaceMatches($interface)) {
                return $endpoint->url;
            }
        }

        return false;
    }
}
