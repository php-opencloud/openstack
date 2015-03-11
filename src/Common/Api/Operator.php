<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;

abstract class Operator implements OperatorInterface
{
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getOperation($name, array $userOptions = [])
    {
        $apiClass = sprintf("%s\\Api", $this->getServiceNamespace());

        if (!method_exists($apiClass, $name)) {
            throw new \Exception(sprintf("Method %s::%s does not exist", $apiClass, $name));
        }

        return new Operation($this->client, $apiClass::$name(), $userOptions);
    }

    protected function getCurrentNamespace()
    {
        return (new \ReflectionClass(get_class($this)))->getNamespaceName();
    }

    protected function execute($name, array $userOptions = [])
    {
        $operation = $this->getOperation($name, $userOptions);

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