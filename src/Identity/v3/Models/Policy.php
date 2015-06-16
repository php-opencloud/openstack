<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsCreatable;
use OpenStack\Common\Resource\IsDeletable;
use OpenStack\Common\Resource\IsListable;
use OpenStack\Common\Resource\IsRetrievable;
use OpenStack\Common\Resource\IsUpdateable;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Policy extends AbstractResource implements IsCreatable, IsListable, IsRetrievable, IsUpdateable, IsDeletable
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