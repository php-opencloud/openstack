<?php

namespace OpenStack\ObjectStore\v1\Models;

use Psr\Http\Message\ResponseInterface;

trait MetadataTrait
{
    public function parseMetadata(ResponseInterface $response)
    {
        $metadata = [];

        foreach ($response->getHeaders() as $header => $value) {
            $position = strpos($header, static::METADATA_PREFIX);
            if ($position === 0) {
                $metadata[ltrim($header, static::METADATA_PREFIX)] = $response->getHeader($header)[0];
            }
        }

        return $metadata;
    }
}
