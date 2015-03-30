<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;

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

    protected function execute(array $definition, array $userOptions = [])
    {
        $operation = $this->getOperation($definition, $userOptions);

        return $this->client->send($operation->createRequest());
    }

    protected function model($name, ResponseInterface $response = null)
    {
        $class = sprintf("%s\\Models\\%s", $this->getServiceNamespace(), $name);

        $model = new $class($this->client);

        if ($response) {
            $model->fromResponse($response);
        }

        return $model;
    }
}