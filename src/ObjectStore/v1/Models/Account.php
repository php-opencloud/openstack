<?php

namespace OpenStack\ObjectStore\v1\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Retrievable;
use GuzzleHttp\Message\ResponseInterface;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Account extends AbstractResource implements Retrievable
{
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

        foreach ($response->getHeaders() as $header => $value) {
            $position = strpos($header, self::METADATA_PREFIX);
            if ($position === 0) {
                $this->metadata[ltrim($header, self::METADATA_PREFIX)] = $response->getHeader($header);
            }
        }
    }

    public function retrieve()
    {
        $response = $this->execute($this->api->headAccount());
        $this->populateFromResponse($response);
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postAccount(), ['metadata' => $metadata]);
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

        $response = $this->execute($this->api->postAccount(), $options);
        return $response->json()['metadata'];
    }

    public function getMetadata()
    {
        $response = $this->execute($this->api->headAccount());
        return $response->json()['metadata'];
    }
}