<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsDeletable;
use OpenStack\Common\Resource\IsListable;
use OpenStack\Common\Resource\IsRetrievable;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Image extends AbstractResource implements IsListable, IsRetrievable, IsDeletable
{
    public $created;
    public $id;
    public $links;
    public $metadata;
    public $minDisk;
    public $minRam;
    public $name;
    public $progress;
    public $status;
    public $updated;

    protected $resourceKey = 'image';
    protected $resourcesKey = 'images';

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->created = new \DateTimeImmutable($this->created);
        $this->updated = new \DateTimeImmutable($this->updated);
    }

    public function retrieve()
    {

    }

    public function delete()
    {
        $this->execute($this->api->deleteImage(), ['id' => (string) $this->id]);
    }

    public function getMetadata()
    {

    }

    public function resetMetadata(array $metadata)
    {

    }

    public function mergeMetadata(array $metadata)
    {

    }

    public function getMetadataItem($key)
    {

    }

    public function deleteMetadataItem($key)
    {

    }
}