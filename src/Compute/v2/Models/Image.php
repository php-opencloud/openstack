<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

/**
 * Represents a Compute v2 Image
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class Image extends AbstractResource implements Listable, Retrievable, Deletable
{
    /** @var string */
    public $id;

    /** @var array */
    public $links;

    /** @var array */
    public $metadata;

    /** @var int */
    public $minDisk;

    /** @var int */
    public $minRam;

    /** @var string */
    public $name;

    /** @var string */
    public $progress;

    /** @var string */
    public $status;

    /** @var \DateTimeImmutable */
    public $created;

    /** @var \DateTimeImmutable */
    public $updated;

    protected $resourceKey = 'image';
    protected $resourcesKey = 'images';

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getImage(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteImage(), ['id' => (string) $this->id]);
    }

    /**
     * Retrieves metadata from the API.
     *
     * @return array
     */
    public function getMetadata()
    {
        $response = $this->execute($this->api->getImageMetadata(), ['id' => $this->id]);
        return Utils::jsonDecode($response)['metadata'];
    }

    /**
     * Resets all the metadata for this image with the values provided. All existing metadata keys
     * will either be replaced or removed.
     *
     * @param array $metadata {@see \OpenStack\Compute\v2\Api::putImageMetadata}
     *
     * @return array
     */
    public function resetMetadata(array $metadata)
    {
        $response = $this->execute($this->api->putImageMetadata(), ['id' => $this->id, 'metadata' => $metadata]);
        return Utils::jsonDecode($response)['metadata'];
    }

    /**
     * Merges the existing metadata for the image with the values provided. Any existing keys
     * referenced in the user options will be replaced with the user's new values. All other
     * existing keys will remain unaffected.
     *
     * @param array $metadata {@see \OpenStack\Compute\v2\Api::postImageMetadata}
     *
     * @return array
     */
    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postImageMetadata(), ['id' => $this->id, 'metadata' => $metadata]);
        return Utils::jsonDecode($response)['metadata'];
    }

    /**
     * Retrieve the value for a specific metadata key.
     *
     * @param string $key {@see \OpenStack\Compute\v2\Api::getImageMetadataKey}
     *
     * @return mixed
     */
    public function getMetadataItem($key)
    {
        $response = $this->execute($this->api->getImageMetadataKey(), ['id' => $this->id, 'key' => $key]);
        return Utils::jsonDecode($response)['metadata'][$key];
    }

    /**
     * Remove a specific metadata key.
     *
     * @param string $key {@see \OpenStack\Compute\v2\Api::deleteImageMetadataKey}
     */
    public function deleteMetadataItem($key)
    {
        $this->execute($this->api->deleteImageMetadataKey(), ['id' => $this->id, 'key' => $key]);
    }
}
