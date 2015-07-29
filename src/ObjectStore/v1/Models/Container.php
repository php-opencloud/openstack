<?php

namespace OpenStack\ObjectStore\v1\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Error\BadResponseError;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasMetadata;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Container extends AbstractResource implements Creatable, Deletable, Retrievable, Listable, HasMetadata
{
    use MetadataTrait;

    const METADATA_PREFIX = 'X-Container-Meta-';

    /** @var int */
    public $objectCount;

    /** @var int */
    public $bytesUsed;

    /** @var string */
    public $name;

    /** @var metadata */
    public $metadata;

    protected $markerKey = 'name';

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->objectCount = $response->getHeader('X-Container-Object-Count');
        $this->bytesUsed = $response->getHeader('X-Container-Bytes-Used');
        $this->metadata = $this->parseMetadata($response);
    }

    public function listObjects(array $options = [], callable $mapFn = null)
    {
        $options = array_merge($options, ['name' => $this->name, 'format' => 'json']);
        $operation = $this->getOperation($this->api->getContainer(), $options);
        return $this->model('Object')->enumerate($operation, $mapFn);
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->headContainer());
        $this->populateFromResponse($response);
    }

    public function create(array $data)
    {
        $response = $this->execute($this->api->putContainer(), $data);

        $this->populateFromResponse($response);
        $this->name = $data['name'];

        return $this;
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteContainer());
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postContainer(), ['name' => $this->name, 'metadata' => $metadata]);
        return $this->parseMetadata($response);
    }

    public function resetMetadata(array $metadata)
    {
        $options = [
            'name'           => $this->name,
            'removeMetadata' => [],
            'metadata'       => $metadata,
        ];

        foreach ($this->getMetadata() as $key => $val) {
            if (!array_key_exists($key, $metadata)) {
                $options['removeMetadata'][$key] = 'True';
            }
        }

        $response = $this->execute($this->api->postContainer(), $options);
        return $this->parseMetadata($response);
    }

    public function getMetadata()
    {
        $response = $this->executeWithState($this->api->headContainer());
        return $this->parseMetadata($response);
    }

    public function getObject($name)
    {
        return $this->model('Object', ['containerName' => $this->name, 'name' => $name]);
    }

    public function objectExists($name)
    {
        try {
            $this->getObject($name)->retrieve();
            return true;
        } catch (BadResponseError $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function createObject(array $data)
    {
        return $this->model('Object')->create($data + ['containerName' => $this->name]);
    }
}