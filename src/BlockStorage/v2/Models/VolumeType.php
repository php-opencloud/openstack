<?php declare (strict_types=1);

namespace OpenStack\BlockStorage\v2\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Updateable;

/**
 * @property \OpenStack\BlockStorage\v2\Api $api
 */
class VolumeType extends AbstractResource implements Listable, Creatable, Updateable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    protected $resourceKey  = 'volume_type';
    protected $resourcesKey = 'volume_types';

    /**
     * @param array $userOptions {@see \OpenStack\BlockStorage\v2\Api::postTypes}
     *
     * @return Creatable
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postTypes(), $userOptions);
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $this->executeWithState($this->api->putType());
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteType());
    }
}
