<?php

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;

/**
 * Represents a Network v2 Network.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class Network extends AbstractResource implements Listable, Retrievable
{
    public $id;
    public $name;
    public $shared;
    public $status;
    public $subnets;

    protected $resourceKey = 'network';
    protected $resourcesKey = 'networks';

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getNetwork(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }
}
