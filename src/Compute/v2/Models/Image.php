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
        $response = $this->execute($this->api->getImage(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteImage(), ['id' => (string) $this->id]);
    }

    public function getMetadata()
    {
        $response = $this->execute($this->api->getImageMetadata(), ['id' => $this->id]);
        return $response->json()['metadata'];
    }

    public function resetMetadata(array $metadata)
    {
        $response = $this->execute($this->api->putImageMetadata(), ['id' => $this->id, 'metadata' => $metadata]);
        return $response->json()['metadata'];
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postImageMetadata(), ['id' => $this->id, 'metadata' => $metadata]);
        return $response->json()['metadata'];
    }

    public function getMetadataItem($key)
    {
        $response = $this->execute($this->api->getImageMetadataKey(), ['id' => $this->id, 'key' => $key]);
        return $response->json()['metadata'][$key];
    }

    public function deleteMetadataItem($key)
    {
        $this->execute($this->api->deleteImageMetadataKey(), ['id' => $this->id, 'key' => $key]);
    }
}