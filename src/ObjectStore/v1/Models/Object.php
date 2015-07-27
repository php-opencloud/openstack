<?php

namespace OpenStack\ObjectStore\v1\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Object extends AbstractResource implements Creatable, Deletable
{
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

    protected $aliases = ['bytes' => 'contentLength'];

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $headers = $response->getHeaders();

        $this->hash = $headers['ETag'];
        $this->contentLength = $headers['Content-Length'];
        $this->lastModified = $headers['Last-Modified'];
        $this->contentType = $headers['Content-Type'];

        foreach ($headers as $header => $value) {
            $position = strpos($headers, self::METADATA_PREFIX);
            if ($position === 0) {
                $this->metadata[substr($header, $position)] = $value;
            }
        }
    }

    public function create(array $data)
    {
        $response = $this->execute($this->api->putObject(), $data);
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
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteObject());
    }

    public function copy(array $options)
    {
        $options += ['name' => $this->name, 'container' => $this->containerName];
        $this->execute($this->api->copyObject(), $options);
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postObject(), ['metadata' => $metadata]);
        return $response->json()['metadata'];
    }

    public function resetMetadata(array $metadata)
    {
        $options = [
            'removeMetadata' => [],
            'metadata'       => $metadata,
        ];

        foreach ($this->getMetadata() as $metadataItem => $val) {
            $options['removeMetadata'][$metadataItem] = true;
        }

        $response = $this->execute($this->api->postObject(), $options);
        return $response->json()['metadata'];
    }

    public function getMetadata()
    {
        $response = $this->execute($this->api->headObject());
        return $response->json()['metadata'];
    }
}