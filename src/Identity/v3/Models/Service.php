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
class Service extends AbstractResource implements IsCreatable, IsListable, IsRetrievable, IsUpdateable, IsDeletable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var string */
    public $description;

    /** @var []Endpoint */
    public $endpoints;

    /** @var array */
    public $links;

    protected $resourceKey = 'service';

    public function create(array $data)
    {
        $response = $this->execute($this->api->postServices(), $data);
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