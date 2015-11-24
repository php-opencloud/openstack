<?php

namespace OpenStack\ObjectStore\v1\Models;

use Psr\Http\Message\ResponseInterface;
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

    /** @var array */
    public $metadata;

    protected $markerKey = 'name';

    /**
     * {@inheritdoc}
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->objectCount = $response->getHeaderLine('X-Container-Object-Count');
        $this->bytesUsed = $response->getHeaderLine('X-Container-Bytes-Used');
        $this->metadata = $this->parseMetadata($response);
    }

    /**
     * Retrieves a collection of object resources in the form of a generator.
     *
     * @param array         $options {@see \OpenStack\ObjectStore\v1\Api::getContainer}
     * @param callable|null $mapFn   Allows a function to be mapped over each element.
     *
     * @return \Generator
     */
    public function listObjects(array $options = [], callable $mapFn = null)
    {
        $options = array_merge($options, ['name' => $this->name, 'format' => 'json']);
        return $this->model(Object::class)->enumerate($this->api->getContainer(), $options, $mapFn);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->headContainer());
        $this->populateFromResponse($response);
    }

    /**
     * @param array $data {@see \OpenStack\ObjectStore\v1\Api::putContainer}
     *
     * @return $this
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->putContainer(), $data);

        $this->populateFromResponse($response);
        $this->name = $data['name'];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteContainer());
    }

    /**
     * {@inheritdoc}
     */
    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postContainer(), ['name' => $this->name, 'metadata' => $metadata]);
        return $this->parseMetadata($response);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        $response = $this->executeWithState($this->api->headContainer());
        return $this->parseMetadata($response);
    }

    /**
     * Retrieves an Object and populates its `name` and `containerName` properties according to the name provided and
     * the name of this container. A HTTP call will not be executed by default - you need to call
     * {@see Object::retrieve} or {@see Object::download} on the returned Object object to do that.
     *
     * @param string $name The name of the object
     *
     * @return Object
     */
    public function getObject($name)
    {
        return $this->model(Object::class, ['containerName' => $this->name, 'name' => $name]);
    }

    /**
     * Identifies whether an object exists in this container.
     *
     * @param string $name The name of the object.
     *
     * @return bool TRUE if the object exists, FALSE if it does not.
     *
     * @throws BadResponseError For any other HTTP error which does not have a 404 Not Found status.
     * @throws \Exception       For any other type of fatal error.
     */
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

    /**
     * Creates a single object according to the values provided.
     *
     * @param array $data {@see \OpenStack\ObjectStore\v1\Api::putObject}
     *
     * @return Object
     */
    public function createObject(array $data)
    {
        return $this->model(Object::class)->create($data + ['containerName' => $this->name]);
    }
}
