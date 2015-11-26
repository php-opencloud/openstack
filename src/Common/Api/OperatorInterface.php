<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;

/**
 * An operator is any resource or service that can invoke and send REST operations. In other words, it
 * is any class that can send requests and receive responses with a HTTP client. To do this
 * it needs two things: a {@see ClientInterface} for handling HTTP transactions and an {@see ApiInterface}
 * for handling how operations are created.
 *
 * @package OpenStack\Common\Api
 */
interface OperatorInterface
{
    /**
     * @param ClientInterface $client The HTTP client responsible for handling HTTP transactions
     * @param ApiInterface    $api    The data API class that dictates how REST operations are structured
     */
    public function __construct(ClientInterface $client, ApiInterface $api);

    /**
     * A convenience method that assembles an operation and sends it to the remote API
     *
     * @param array $definition The data that dictates how the operation works
     * @param array $userValues The user-defined values that populate the request
     * @param bool  $async      Indicates whether the operation should be executed asychronously. If set to TRUE, a
     *                          {@see PromiseInterface} is returned. If FALSE is provided (the default), a
     *                          {@see ResponseInterface} is returned.
     *
     * @return \Psr\Http\Message\ResponseInterface|\GuzzleHttp\Promise\PromiseInterface
     */
    public function execute(array $definition, array $userValues = [], $async = false);

    /**
     * @param string $name The name of the model class.
     * @param mixed  $data Either a {@see ResponseInterface} or data array that will populate the newly
     *                     created model class.
     *
     * @return \OpenStack\Common\Resource\ResourceInterface
     */
    public function model($name, $data = null);
}
