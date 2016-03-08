<?php

namespace OpenCloud\Common\Api;

use function GuzzleHttp\uri_template;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use OpenCloud\Common\Resource\ResourceInterface;
use OpenCloud\Common\Transport\RequestSerializer;
use Psr\Http\Message\ResponseInterface;

/**
 * {@inheritDoc}
 */
abstract class Operator implements OperatorInterface
{
    /** @var ClientInterface */
    protected $client;

    /** @var ApiInterface */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function __construct(ClientInterface $client, ApiInterface $api)
    {
        $this->client = $client;
        $this->api = $api;
    }

    /**
     * Magic method for dictating how objects are rendered when var_dump is called.
     * For the benefit of users, extremely verbose and heavy properties (such as HTTP clients) are
     * removed to provide easier access to normal state, such as resource attributes.
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function __debugInfo()
    {
        $excludedVars = ['client', 'errorBuilder', 'api'];

        $output = [];

        foreach (get_object_vars($this) as $key => $val) {
            if (!in_array($key, $excludedVars)) {
                $output[$key] = $val;
            }
        }

        return $output;
    }

    /**
     * Retrieves a populated Operation according to the definition and values provided. A
     * HTTP client is also injected into the object to allow it to communicate with the remote API.
     *
     * @param array $definition The data that dictates how the operation works
     *
     * @return Operation
     */
    public function getOperation(array $definition)
    {
        return new Operation($definition);
    }

    protected function sendRequest(Operation $operation, array $userValues = [], $async = false)
    {
        $operation->validate($userValues);

        $options = (new RequestSerializer)->serializeOptions($operation, $userValues);
        $method = $async ? 'requestAsync' : 'request';
        $uri = uri_template($operation->getPath(), $userValues);

        return $this->client->$method($operation->getMethod(), $uri, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $definition, array $userValues = [])
    {
        return $this->sendRequest($this->getOperation($definition), $userValues);
    }

    /**
     * {@inheritDoc}
     */
    public function executeAsync(array $definition, array $userValues = [])
    {
        return $this->sendRequest($this->getOperation($definition), $userValues, true);
    }

    /**
     * {@inheritDoc}
     */
    public function model($class, $data = null)
    {
        $model = new $class($this->client, $this->api);

        // @codeCoverageIgnoreStart
        if (!$model instanceof ResourceInterface) {
            throw new \RuntimeException(sprintf('%s does not implement %s', $class, ResourceInterface::class));
        }
        // @codeCoverageIgnoreEnd

        if ($data instanceof ResponseInterface) {
            $model->populateFromResponse($data);
        } elseif (is_array($data)) {
            $model->populateFromArray($data);
        }

        return $model;
    }

    /**
     * Will create a new instance of this class with the current HTTP client and API injected in. This
     * is useful when enumerating over a collection since multiple copies of the same resource class
     * are needed.
     *
     * @return static
     */
    public function newInstance()
    {
        return new static($this->client, $this->api);
    }

    /**
     * @return \GuzzleHttp\Psr7\Uri
     */
    protected function getHttpBaseUrl()
    {
        return $this->client->getConfig('base_uri');
    }

    /**
     * Magic method which intercepts async calls, finds the sequential version, and wraps it in a
     * {@see Promise} object. In order for this to happen, the called methods need to be in the
     * following format: `createAsync`, where `create` is the sequential method being wrapped.
     *
     * @param $methodName The name of the method being invoked.
     * @param $args       The arguments to be passed to the sequential method.
     *
     * @throws \RuntimeException If method does not exist
     *
     * @return Promise
     */
    public function __call($methodName, $args)
    {
        $e = function ($name) {
            return new \RuntimeException(sprintf('%s::%s is not defined', get_class($this), $name));
        };

        if (substr($methodName, -5) === 'Async') {
            $realMethod = substr($methodName, 0, -5);
            if (!method_exists($this, $realMethod)) {
                throw $e($realMethod);
            }

            $promise = new Promise(
                function () use (&$promise, $realMethod, $args) {
                    $value = call_user_func_array([$this, $realMethod], $args);
                    $promise->resolve($value);
                }
            );

            return $promise;
        }

        throw $e($methodName);
    }
}
