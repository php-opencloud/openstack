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
    public $adminStateUp;

    protected $aliases = [
        'admin_state_up' => 'adminStateUp',
    ];

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

    /**
     * {@inheritDoc}
     *
     * @param array $userOptions {@see \OpenStack\Networking\v2\Api::postNetwork}
     */
    public function create(array $userOptions)
    {
        $response = $this->execute($this->api->postNetwork(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->execute($this->api->putNetwork(), $this->getAttrs(['id', 'name', 'shared', 'adminStateUp']));

        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteNetwork(), $this->getAttrs(['id']));
    }
}
