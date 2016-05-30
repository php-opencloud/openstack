<?php declare (strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\OperatorResource;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Retrievable;

/**
 * Represents a Compute v2 Flavor.
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class Flavor extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    /** @var int */
    public $disk;

    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $ram;

    /** @var int */
    public $swap;

    /** @var int */
    public $vcpus;

    /** @var array */
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

    /**
     * {@inheritDoc}
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postFlavors(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteFlavor(), ['id' => (string) $this->id]);
    }
}
