<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;

/**
 * Represents a Compute v2 Flavor.
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class Flavor extends AbstractResource implements Listable, Retrievable
{
    public $disk;
    public $id;
    public $name;
    public $ram;
    public $vcpus;
    public $links;

    protected $resourceKey = 'flavor';
    protected $resourcesKey = 'flavors';

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getFlavor(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }
}