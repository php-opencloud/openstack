<?php

namespace OpenStack\ObjectStore\v1\Models;

use GuzzleHttp\Message\ResponseInterface;

trait MetadataTrait
{
    public function parseMetadata(ResponseInterface $response)
    {
        $metadata = [];

        foreach ($response->getHeaders() as $header => $value) {
            $position = strpos($header, static::METADATA_PREFIX);
            if ($position === 0) {
                $metadata[ltrim($header, static::METADATA_PREFIX)] = $response->getHeader($header);
            }
        }

        return $metadata;
    }
}