<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsListable;
use OpenStack\Common\Resource\IsRetrievable;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Flavor extends AbstractResource implements IsListable, IsRetrievable
{
    public $disk;
    public $id;
    public $name;
    public $ram;
    public $vcpus;
    public $links;

    protected $resourceKey = 'flavor';
    protected $resourcesKey = 'flavors';

    public function retrieve()
    {
        $response = $this->execute($this->api->getFlavor(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }
}