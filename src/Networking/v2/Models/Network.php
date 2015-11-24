<?php

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

/**
 * Represents a Networking v2 Network.
 *
 * @property \OpenStack\Networking\v2\Api $api
 */
class Network extends AbstractResource implements Listable, Retrievable, Creatable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var bool */
    public $shared;

    /** @var string */
    public $status;

    /** @var array */
    public $subnets;

    /** @var string */
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
     * Creates multiple networks in a single request.
     *
     * @param array $data {@see \OpenStack\Networking\v2\Api::postNetworks}
     * @return Network[]
     */
    public function bulkCreate(array $data)
    {
        $response = $this->execute($this->api->postNetworks(), ['networks' => $data]);
        $networksData = Utils::jsonDecode($response)['networks'];

        $networks = [];
        foreach ($networksData as $resourceData) {
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
        $this->executeWithState($this->api->deleteNetwork());
    }
}
