<?php

namespace OpenStack\Common\Resource;

use GuzzleHttp\Message\ResponseInterface;

interface HasMetadata
{
    public function getMetadata();

    public function mergeMetadata(array $metadata);

    public function resetMetadata(array $metadata);

    public function parseMetadata(ResponseInterface $response);
}