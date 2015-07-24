<?php

namespace OpenStack\ObjectStore\v1\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Container extends AbstractResource implements Creatable, Deletable, Retrievable, Listable
{
    const METADATA_PREFIX = 'X-Container-Meta-';

    /** @var int */
    public $count;

    /** @var int */
    public $bytes;

    /** @var string */
    public $name;

    /** @var metadata */
    public $metadata;

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $headers = $response->getHeaders();

        $this->count = $headers['X-Container-Object-Count'];
        $this->bytes = $headers['X-Container-Bytes-Used'];

        foreach ($headers as $header => $value) {
            $position = strpos($headers, self::METADATA_PREFIX);
            if ($position === 0) {
                $this->metadata[substr($header, $position)] = $value;
            }
        }
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->headContainer());
        $this->populateFromResponse($response);
    }

    public function create(array $data)
    {
        $response = $this->execute($this->api->postContainer(), $data);
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteContainer());
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postContainer(), ['metadata' => $metadata]);
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

        $response = $this->execute($this->api->postContainer(), $options);
        return $response->json()['metadata'];
    }

    public function getMetadata()
    {
        $response = $this->execute($this->api->headContainer());
        return $response->json()['metadata'];
    }

    public function getObject($name)
    {
        return $this->model('Object', ['containerName' => $this->name, 'name' => $name]);
    }

    public function createObject(array $data)
    {
        return $this->model('Object')->create($data);
    }
}