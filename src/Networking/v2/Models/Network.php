<?php declare (strict_types=1);

namespace OpenStack\Networking\v2\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Retrievable;

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
        $response = $this->execute($this->api->getNetwork(), ['id' => (string)$this->id]);
        $this->populateFromResponse($response);
    }

    /**
     * Creates multiple networks in a single request.
     *
     * @param array $data {@see \OpenStack\Networking\v2\Api::postNetworks}
     *
     * @return Network[]
     */
    public function bulkCreate(array $data): array
    {
        $response = $this->execute($this->api->postNetworks(), ['networks' => $data]);
        return $this->extractMultipleInstances($response);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Networking\v2\Api::postNetwork}
     */
    public function create(array $data): Creatable
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
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteNetwork());
    }
}
