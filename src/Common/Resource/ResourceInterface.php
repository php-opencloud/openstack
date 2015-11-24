<?php

namespace OpenStack\Common\Resource;

use Psr\Http\Message\ResponseInterface;

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
     *
     * @return self
     */
    public function populateFromResponse(ResponseInterface $response);

    /**
     * @param array $data
     * @return mixed
     */
    public function populateFromArray(array $data);
}
