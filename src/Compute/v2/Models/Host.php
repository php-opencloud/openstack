<?php declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\HasWaiterTrait;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Transport\Utils;
use OpenStack\BlockStorage\v2\Models\VolumeAttachment;
use OpenStack\Compute\v2\Enum;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;
use Psr\Http\Message\ResponseInterface;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Server extends OperatorResource implements
    Creatable,
    Updateable,
    Deletable,
    Retrievable,
    Listable
{
    use HasWaiterTrait;

    /** @var string */
    public $id;

    /** @var string */
    public $name;

    protected $resourceKey = 'os-host';
    protected $resourcesKey = 'os-hosts';
    protected $markerKey = 'id';

    /**
     * {@inheritDoc}
     *
     * @param array $userOptions {@see \OpenStack\Compute\v2\Api::postServer}
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postServer(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {
        $response = $this->execute($this->api->putServer(), $this->getAttrs(['id', 'name', 'ipv4', 'ipv6']));
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteServer(), $this->getAttrs(['id']));
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getServer(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }
}
