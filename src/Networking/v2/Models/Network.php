<?php

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Retrievable;

/**
 * Represents a Network v2 Network.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class Network extends AbstractResource implements Listable, Retrievable, Creatable, Deletable
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
     * @param array $data {@see \OpenStack\Networking\v2\Api::postNetworks}
     */
    public function bulkCreate(array $data)
    {
        $response = $this->execute($this->api->postNetworks(), [
            'networks' => $data,
        ]);
        $body = $response->json();
        $json = $body['networks'];

        $networks = [];
        foreach($json as $resourceData) {
            $resource = $this->newInstance();
            $resource->populateFromArray($resourceData);
            $networks[] = $resource;
        }

        return $networks;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Networking\v2\Api::postNetwork}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postNetwork(), $data);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->putNetwork());
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
