<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\Exception\RequestException;
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

    /**
     * @param array           $definition The data definition (in array form) that will populate this
     *                                    operation. Usually this is retrieved from an {@see ApiInterface}
     *                                    object method.
     */
    public function __construct(array $definition)
    {
        $this->method = $definition['method'];
        $this->path = $definition['path'];

        if (isset($definition['jsonKey'])) {
            $this->jsonKey = $definition['jsonKey'];
        }

        $this->params = self::toParamArray($definition['params']);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getMethod()
    {
        return $this->method;
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
     * @param $name
     *
     * @return Parameter
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    public function getJsonKey()
    {
        return $this->jsonKey;
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
