<?php

namespace OpenStack\Common\Resource;

use GuzzleHttp\Message\ResponseInterface;

/**
 * Represents an API resource.
 *
 * @package OpenStack\Common\Resource
 */
interface ResourceInterface
{
    /**
     * All models which represent an API resource should be able to be populated
     * from a {@see ResponseInterface} object.
     *
     * @param ResponseInterface $response
     * @return mixed
     */
    public function fromResponse(ResponseInterface $response);
} 