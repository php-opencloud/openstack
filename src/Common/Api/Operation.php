<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Utils;

/**
 * This class represents an OpenStack API operation. It encapsulates most aspects of the REST operation: its HTTP
 * method, the URL path, its top-level JSON key, and all of its {@see Parameter} objects.
 *
 * An operation not only represents a remote operation, but it also provides the mechanism for executing it
 * over HTTP. To do this, it uses a {@see ClientInterface} that allows a {@see GuzzleHttp\Message\Request}
 * to be created from the user values provided. Once this request is assembled, it is then sent to the
 * remote API and the response is returned to whoever first invoked the Operation class.
 *
 * @package OpenStack\Common\Api
 */
class Operation
{
    /** @var string The HTTP method */
    private $method;

    /** @var string The URL path */
    private $path;

    /** @var string The top-level JSON key */
    private $jsonKey;

    /** @var []Parameter The parameters of this operation */
    private $params;

    /** @var ClientInterface The HTTP client responsible for creating and sending requests */
    private $client;

    /** @var array The user-defined values that will populate this request */
    private $userValues;

    /**
     * @param ClientInterface $client     The HTTP client
     * @param array           $definition The data definition (in array form) that will populate this
     *                                    operation. Usually this is retrieved from an {@see ApiInterface}
     *                                    object method.
     * @param array           $userValues The user-defined values.
     */
    public function __construct(ClientInterface $client, array $definition, array $userValues = [])
    {
        $this->method = $definition['method'];
        $this->path   = $definition['path'];

        if (isset($definition['jsonKey'])) {
            $this->jsonKey = $definition['jsonKey'];
        }

        $this->params = self::toParamArray($definition['params']);
        $this->client = $client;
        $this->userValues = $userValues;
    }

    /**
     * Allows for the setting or overriding of a user value. This is useful for when an operation
     * needs to be updated after creation time (to update the "marker" query for example).
     *
     * @param string $key   The name of the value being updated
     * @param mixed  $value The (new) value being set
     */
    public function setValue($key, $value)
    {
        $this->userValues[$key] = $value;
    }

    /**
     * This will retrieve a previously set user value.
     *
     * @param $key The name of the user value
     *
     * @return mixed|null
     */
    public function getValue($key)
    {
        return isset($this->userValues[$key]) ? $this->userValues[$key] : null;
    }

    /**
     * Indicates whether this operation supports a parameter.
     *
     * @param $key The name of a parameter
     *
     * @return bool
     */
    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * A convenience method that will take a generic array of data and convert it into an array of
     * {@see Parameter} objects.
     *
     * @param array $data A generic data array
     *
     * @return array
     */
    public static function toParamArray(array $data)
    {
        $params = [];

        foreach ($data as $name => $param) {
            $params[$name] = new Parameter($param + ['name' => $name]);
        }

        return $params;
    }

    /**
     * Internal method that serializes the JSON body for a request
     *
     * @return array
     */
    private function serializeJson()
    {
        $serializer = new JsonSerializer();

        $options = $this->jsonKey ? ['jsonKey' => $this->jsonKey] : [];

        return $serializer->serialize($this->userValues, $this->params, $options);
    }

    /**
     * Internal method that serializes all of the HTTP headers for a request
     *
     * @return array
     */
    private function serializeHeaders()
    {
        $serializer = new HeaderSerializer();

        return $serializer->serialize($this->userValues, $this->params);
    }

    /**
     * Internal method that serializes all of the query parameters for a request's URL
     *
     * @param string $url The input URL
     *
     * @return \GuzzleHttp\Url
     */
    private function serializeQuery($url)
    {
        $serializer = new QuerySerializer();

        return $serializer->serialize($this->userValues, $this->params, $url);
    }

    /**
     * This method will take all of the user-provided values and populate them into a
     * {@see \GuzzleHttp\Message\RequestInterface} object according to each parameter schema.
     * Headers and URL query parameters will be set, along with the JSON body.
     *
     * In other words, it allows for the easy creation of a fully populated HTTP request in
     * accordance with the expectations of the remote API.
     *
     * @return \GuzzleHttp\Message\RequestInterface
     * @throws \Exception
     */
    public function createRequest()
    {
        $this->validate($this->userValues);

        $options = [];

        if (!empty($json = $this->serializeJson())) {
            $options['json'] = $json;
        }

        if (!empty($headers = $this->serializeHeaders())) {
            $options['headers'] = $headers;
        }

        $url = $this->serializeQuery(
            Utils::uriTemplate($this->path, $this->userValues)
        );

        return $this->client->createRequest($this->method, $url, $options);
    }

    /**
     * This will first create a request {@see createRequest()} and then send it to the remote API.
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function send()
    {
        return $this->client->send($this->createRequest());
    }

    /**
     * This method will validate all of the user-provided values and throw an exception if any
     * failures are detected. This is useful for basic sanity-checking before a request is
     * serialized and sent to the API.
     *
     * @param array $userValues The user-defined values
     *
     * @return bool       TRUE if validation passes
     * @throws \Exception If validate fails
     */
    public function validate(array $userValues)
    {
        // Make sure the user has not provided undefined keys
        if (!empty($disallowedKeys = array_keys(array_diff_key($userValues, $this->params)))) {
            throw new \Exception(sprintf(
                'The following keys are not supported: %s', implode($disallowedKeys, ', ')
            ));
        }

        foreach ($this->params as $paramName => $param) {
            // Check that all required options have been provided
            if ($param->isRequired() && !array_key_exists($paramName, $userValues)) {
                throw new \Exception(sprintf('"%s" is a required option, but it was not provided', $paramName));
            }

            // Check that the user value is valid and well-formed
            if (array_key_exists($paramName, $userValues)) {
                $param->validate($userValues[$paramName]);
            }
        }

        return true;
    }
}
