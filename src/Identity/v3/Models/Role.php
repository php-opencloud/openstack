<?php declare (strict_types=1);

namespace OpenStack\Identity\v3\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Listable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Role extends AbstractResource implements Creatable, Listable, Deletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var array */
    public $links;

    protected $resourceKey = 'role';
    protected $resourcesKey = 'roles';

    /**
     * {@inheritDoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postRoles}
     */
    public function create(array $data): Creatable
    {
        $response = $this->execute($this->api->postRoles(), $data);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteRole());
    }
}
