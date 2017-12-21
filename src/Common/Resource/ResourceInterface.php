<?php declare(strict_types=1);

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
     * @return ResourceInterface
     */
    public function populateFromResponse(ResponseInterface $response): ResourceInterface;

    /**
     * @param array $data
     * @return ResourceInterface
     */
    public function populateFromArray(array $data): ResourceInterface;

    /**
     * @param string $name The name of the model class.
     * @param mixed  $data Either a {@see ResponseInterface} or data array that will populate the newly
     *                     created model class.
     *
     * @return \OpenStack\Common\Resource\ResourceInterface
     */
    public function model(string $class, $data = null): ResourceInterface;
}
