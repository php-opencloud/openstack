<?php

namespace OpenStack\Common\Transport;

use function GuzzleHttp\uri_template;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\modify_request;

use OpenStack\Common\Api\JsonSerializer;
use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Parameter;

class RequestSerializer
{
    public static function serializeOptions(Operation $operation, array $userValues = [])
    {
        $options = ['headers' => []];

        $jsonSerializer = new JsonSerializer();

        foreach ($userValues as $paramName => $paramValue) {
            $schema = $operation->getParam($paramName);

            switch ($schema->getLocation()) {
                case 'query':
                    $options['query'][$schema->getName()] = $paramValue;
                    break;
                case 'header':
                    $options['headers'] += $this->parseHeader($schema, $paramName, $paramValue);
                    break;
                case 'json':
                    $options['json'] = $jsonSerializer->stockJson($schema, $paramValue, $options['json']);
                    break;
                case 'raw':
                    $options['body'] = $paramValue;
                    break;
            }
        }

        return $options;
    }

    private function parseHeader(Parameter $param, $name, $value)
    {
        if ($name == 'metadata' || $name == 'removeMetadata') {
            foreach ($value as $key => $keyVal) {
                $schema = $param->getItemSchema() ?: new Parameter(['prefix' => $param->getPrefix()]);
                $this->parseHeader($schema, $key, $keyVal);
            }
        }

        return is_string($value) || is_numeric($value)
            ? [$param->getPrefix() . $name => $value]
            : [];
    }
}