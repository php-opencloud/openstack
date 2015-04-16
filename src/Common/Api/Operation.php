<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Utils;

class Operation
{
    private $method;
    private $path;
    private $jsonKey;
    private $params;

    private $client;
    private $userValues;

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

    public function setValue($key, $value)
    {
        $this->userValues[$key] = $value;
    }

    public function getValue($key)
    {
        return isset($this->userValues[$key]) ? $this->userValues[$key] : null;
    }

    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }

    public static function toParamArray(array $data)
    {
        $params = [];

        foreach ($data as $name => $param) {
            $params[$name] = new Parameter($param + ['name' => $name]);
        }

        return $params;
    }

    private function serializeJson()
    {
        $serializer = new JsonSerializer();

        $options = $this->jsonKey ? ['jsonKey' => $this->jsonKey] : [];

        return $serializer->serialize($this->userValues, $this->params, $options);
    }

    private function serializeHeaders()
    {
        $serializer = new HeaderSerializer();

        return $serializer->serialize($this->userValues, $this->params);
    }

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

        $uriPath = Utils::uriTemplate($this->path, $this->userValues);

        return $this->client->createRequest($this->method, $uriPath, $options);
    }

    public function send()
    {
        return $this->client->send($this->createRequest());
    }

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
