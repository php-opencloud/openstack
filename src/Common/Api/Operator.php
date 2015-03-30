<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\ResourceInterface;

abstract class Operator implements OperatorInterface
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getOperation(array $definition, array $userOptions = [])
    {
        return new Operation($this->client, $definition, $userOptions);
    }

    public function execute(array $definition, array $userOptions = [])
    {
        $operation = $this->getOperation($definition, $userOptions);

        return $this->client->send($operation->createRequest());
    }

    protected function model($name, $data = null)
    {
        $class = sprintf("%s\\Models\\%s", $this->getServiceNamespace(), $name);

        $model = new $class($this->client);

        if (!$model instanceof ResourceInterface) {
            throw new \RuntimeException(sprintf('%s does not implement %s', $class, ResourceInterface::class));
        }

        if ($data instanceof ResponseInterface) {
            $model->populateFromResponse($data);
        } elseif (is_array($data)) {
            $model->populateFromArray($data);
        }

        return $model;
    }

    abstract protected function getServiceNamespace();
}