<?php declare(strict_types=1);

namespace OpenStack\ObjectStore\v1\Models;

use GuzzleHttp\Psr7\Uri;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasMetadata;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Object extends OperatorResource implements Creatable, Deletable, HasMetadata
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

    /**
     * {@inheritdoc}
     */
    public function populateFromResponse(ResponseInterface $response): self
    {
        parent::populateFromResponse($response);

        $this->hash = $response->getHeaderLine('ETag');
        $this->contentLength = $response->getHeaderLine('Content-Length');
        $this->lastModified = $response->getHeaderLine('Last-Modified');
        $this->contentType = $response->getHeaderLine('Content-Type');
        $this->metadata = $this->parseMetadata($response);

        return $this;
    }

    /**
     * Retrieves the public URI for this resource.
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getPublicUri(): Uri
    {
        return Utils::addPaths($this->getHttpBaseUrl(), $this->containerName, $this->name);
    }

    /**
     * @param array $data {@see \OpenStack\ObjectStore\v1\Api::putObject}
     *
     * @return $this
     */
    public function create(array $data): Creatable
    {
        $response = $this->execute($this->api->putObject(), $data + ['containerName' => $this->containerName]);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->headObject());
        $this->populateFromResponse($response);
    }

    /**
     * This call will perform a `GET` HTTP request for the given object and return back its content in the form of a
     * Guzzle Stream object. Downloading an object will transfer all of the content for an object, and is therefore
     * distinct from fetching its metadata (a `HEAD` request). The body of an object is not fetched by default to
     * improve performance when handling large objects.
     *
     * @return StreamInterface
     */
    public function download(): StreamInterface
    {
        $response = $this->executeWithState($this->api->getObject());
        $this->populateFromResponse($response);
        return $response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteObject());
    }

    /**
     * @param array $options {@see \OpenStack\ObjectStore\v1\Api::copyObject}
     */
    public function copy(array $options)
    {
        $options += ['name' => $this->name, 'containerName' => $this->containerName];
        $this->execute($this->api->copyObject(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeMetadata(array $metadata)
    {
        $options = [
            'containerName' => $this->containerName,
            'name'          => $this->name,
            'metadata'      => array_merge($metadata, $this->getMetadata()),
        ];

        $response = $this->execute($this->api->postObject(), $options);
        $this->metadata = $this->parseMetadata($response);
    }

    /**
     * {@inheritdoc}
     */
    public function resetMetadata(array $metadata)
    {
        $options = [
            'containerName'  => $this->containerName,
            'name'           => $this->name,
            'metadata'       => $metadata,
        ];

        $response = $this->execute($this->api->postObject(), $options);
        $this->metadata = $this->parseMetadata($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        $response = $this->executeWithState($this->api->headObject());
        return $this->parseMetadata($response);
    }
}
