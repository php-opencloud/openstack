<?php

namespace OpenStack\ObjectStore\v1\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\HasMetadata;
use OpenStack\Common\Resource\Retrievable;
use GuzzleHttp\Message\ResponseInterface;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Account extends AbstractResource implements Retrievable, HasMetadata
{
    use MetadataTrait;

    const METADATA_PREFIX = 'X-Account-Meta-';

    /** @var int */
    public $objectCount;

    /** @var int */
    public $bytesUsed;

    /** @var int */
    public $containerCount;

    /** @var array */
    public $metadata;

    /** @var string */
    public $tempUrl;

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->containerCount = $response->getHeader('X-Account-Container-Count');
        $this->objectCount = $response->getHeader('X-Account-Object-Count');
        $this->bytesUsed = $response->getHeader('X-Account-Bytes-Used');
        $this->tempUrl = $response->getHeader('X-Account-Meta-Temp-URL-Key');
        $this->metadata = $this->parseMetadata($response);
    }

    public function retrieve()
    {
        $response = $this->execute($this->api->headAccount());
        $this->populateFromResponse($response);
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postAccount(), ['metadata' => $metadata]);
        return $this->parseMetadata($response);
    }

    public function resetMetadata(array $metadata)
    {
        $options = [
            'removeMetadata' => [],
            'metadata'       => $metadata,
        ];

        foreach ($this->getMetadata() as $key => $val) {
            if (!array_key_exists($key, $metadata)) {
                $options['removeMetadata'][$key] = 'True';
            }
        }

        $response = $this->execute($this->api->postAccount(), $options);
        return $this->parseMetadata($response);
    }

    public function getMetadata()
    {
        $response = $this->execute($this->api->headAccount());
        return $this->parseMetadata($response);
    }
}