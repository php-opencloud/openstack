<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Error\Builder;
use OpenStack\Common\Resource\ResourceInterface;

abstract class Operator implements OperatorInterface
{
    private $client;
    private $errorBuilder;
    protected $api;

    public function __construct(ClientInterface $client, ApiInterface $api)
    {
        $this->client = $client;
        $this->api = $api;

        $this->errorBuilder = new Builder();

        $this->client->getEmitter()->attach($this->errorBuilder);
    }

    public function getOperation(array $definition, array $userOptions = [])
    {
        return new Operation($this->client, $definition, $userOptions);
    }

    public function execute(array $definition, array $userOptions = [])
    {
        $operation = $this->getOperation($definition, $userOptions);
        return $operation->send();
    }

    /**
     * @param $name
     * @param null $data
     *
     * @return ResourceInterface
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

    public function getCurrentNamespace()
    {
        return (new \ReflectionClass(get_class($this)))->getNamespaceName();
    }

    public function newInstance()
    {
        return new static($this->client, $this->api);
    }

    abstract protected function getServiceNamespace();
}