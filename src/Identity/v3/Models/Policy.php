<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Policy extends AbstractResource implements Creatable, Listable, Retrievable, Updateable, Deletable
{
    /** @var string */
    public $blob;

    /** @var string */
    public $id;

    /** @var array */
    public $links;

    /** @var string */
    public $projectId;

    /** @var string */
    public $type;

    /** @var string */
    public $userId;

    protected $aliases = [
        'project_id' => 'projectId',
        'user_id' => 'userId'
    ];

    public function create(array $data)
    {
        $response = $this->execute($this->api->postPolicies(), $data);
        $this->populateFromResponse($response);
        return $this;
    }

    public function retrieve()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }
}