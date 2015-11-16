<?php

namespace OpenStack\Common\Api;

use function GuzzleHttp\uri_template;

use GuzzleHttp\ClientInterface;
use OpenStack\Common\Resource\ResourceInterface;
use OpenStack\Common\Transport\RequestSerializer;
use Psr\Http\Message\ResponseInterface;

/**
 * {@inheritDoc}
 */
abstract class Operator implements OperatorInterface
{
    /** @var ClientInterface */
    private $client;

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
     * @param array $definition  The data that dictates how the operation works
     *
     * @return Operation
     */
    public function getOperation(array $definition)
    {
        return new Operation($definition);
    }

    protected function sendRequest(Operation $operation, array $userValues = [], $async = false)
    {
        $uri     = uri_template($operation->getPath(), $userValues);
        $options = RequestSerializer::serializeOptions($operation, $userValues);
        $method  = $async ? 'requestAsync' : 'request';

        return $this->client->$method($operation->getMethod(), $uri, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function executeAsync(array $definition, array $userValues = [])
    {
        return $this->sendRequest($this->getOperation($definition), $userValues, true);
    }

    public function execute(array $definition, array $userValues = [])
    {
        return $this->sendRequest($this->getOperation($definition), $userValues);
    }

    /**
     * {@inheritDoc}
     *
     * Refer to {@see getServiceNamespace()} for more information about how model namespaces are resolved.
     */
    public function model($name, $data = null)
    {
        $class = sprintf("%s\\Models\\%s", $this->getServiceNamespace(), $name);

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
     * @return string
     */
    public function getCurrentNamespace()
    {
        return (new \ReflectionClass(get_class($this)))->getNamespaceName();
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
     * Determines which root namespace to use when instantiating a new model. For example, if a service class
     * is invoking the model, it will use ``__NAMESPACE__\Models`` as the root namespace; for models creating
     * other models, it will just use ``__NAMESPACE__``.
     *
     * @return string
     */
    abstract protected function getServiceNamespace();
}