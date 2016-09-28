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
class Host extends OperatorResource implements
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
     */
    public function retrieve()
    {
      var_dump($this->execute($this->api->getHost(), $this->getAttrs(['name']));
        $response = $this->execute($this->api->getHost(), $this->getAttrs(['name']));
        $this->populateFromResponse($response);
    }
}
