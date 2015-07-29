<?php

namespace OpenStack\ObjectStore\v1\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasMetadata;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Object extends AbstractResource implements Creatable, Deletable, HasMetadata
{
    use MetadataTrait;

    const METADATA_PREFIX = 'X-Object-Meta-';

    /** @var string */
    public $containerName;

    /** @var string */
    public $name;

    /** @var string */
    public $hash;

    /** @var string */
    public $contentType;

    /** @var int */
    public $contentLength;

    /** @var string */
    public $lastModified;

    /** @var array */
    public $metadata;

    protected $markerKey = 'name';
    protected $aliases = ['bytes' => 'contentLength'];

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->hash = $response->getHeader('ETag');
        $this->contentLength = $response->getHeader('Content-Length');
        $this->lastModified = $response->getHeader('Last-Modified');
        $this->contentType = $response->getHeader('Content-Type');
        $this->metadata = $this->parseMetadata($response);
    }

    /**
     * @param array $data
     *
     * @return $this|\OpenStack\Common\Resource\ResourceInterface|void
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->putObject(), $data + ['containerName' => $this->containerName]);
        return $this->populateFromResponse($response);
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->headObject());
        $this->populateFromResponse($response);
    }

    public function download()
    {
        $response = $this->executeWithState($this->api->getObject());
        return $response->getBody();
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteObject());
    }

    public function copy(array $options)
    {
        $options += ['name' => $this->name, 'containerName' => $this->containerName];
        $this->execute($this->api->copyObject(), $options);
    }

    public function mergeMetadata(array $metadata)
    {
        $options = ['containerName' => $this->containerName, 'name' => $this->name, 'metadata' => $metadata];
        $response = $this->execute($this->api->postObject(), $options);
        return $this->parseMetadata($response);
    }

    public function resetMetadata(array $metadata)
    {
        $options = [
            'containerName'  => $this->containerName,
            'name'           => $this->name,
            'removeMetadata' => [],
            'metadata'       => $metadata,
        ];

        foreach ($this->getMetadata() as $key => $val) {
            if (!array_key_exists($key, $metadata)) {
                $options['removeMetadata'][$key] = 'True';
            }
        }

        $response = $this->execute($this->api->postObject(), $options);
        return $this->parseMetadata($response);
    }

    public function getMetadata()
    {
        $response = $this->executeWithState($this->api->headObject());
        return $this->parseMetadata($response);
    }
}