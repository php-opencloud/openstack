<?php

namespace OpenStack\Common\Transport;

use function GuzzleHttp\uri_template;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\modify_request;

use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Parameter;

class RequestSerializer
{
    private $jsonSerializer;

    public function __construct(JsonSerializer $jsonSerializer = null)
    {
        $this->jsonSerializer = $jsonSerializer ?: new JsonSerializer();
    }

    public function serializeOptions(Operation $operation, array $userValues = [])
    {
        $options = ['headers' => []];

        foreach ($userValues as $paramName => $paramValue) {
            if (null === ($schema = $operation->getParam($paramName))) {
                continue;
            }

            switch ($schema->getLocation()) {
                case 'query':
                    $options['query'][$schema->getName()] = $paramValue;
                    break;
                case 'header':
                    $options['headers'] += $this->parseHeader($schema, $paramName, $paramValue);
                    break;
                case 'json':
                    $json = isset($options['json']) ? $options['json'] : [];
                    $options['json'] = $this->jsonSerializer->stockJson($schema, $paramValue, $json);
                    break;
                case 'raw':
                    $options['body'] = $paramValue;
                    break;
            }
        }

        if (!empty($options['json']) && ($key = $operation->getJsonKey())) {
            $options['json'] = [$key => $options['json']];
        }

        return $options;
    }

    private function parseHeader(Parameter $param, $name, $value)
    {
        if ($name == 'metadata' || $name == 'removeMetadata') {
            $headers = [];
            foreach ($value as $key => $keyVal) {
                $schema = $param->getItemSchema() ?: new Parameter(['prefix' => $param->getPrefix(), 'name' => $key]);
                $headers += $this->parseHeader($schema, $key, $keyVal);
            }
            return $headers;
        }

        return is_string($value) || is_numeric($value)
            ? [$param->getPrefix() . $param->getName() => $value]
            : [];
    }
}
