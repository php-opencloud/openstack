<?php

namespace OpenStack\Common\Transport;

use function GuzzleHttp\uri_template;
use function GuzzleHttp\Psr7\build_query;
use function GuzzleHttp\Psr7\modify_request;
use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Parameter;

class RequestSerializer
{
    public static function serializeOptions(Operation $operation, array $userValues = [])
    {
        $options = ['headers' => []];

        $jsonSerializer = new JsonSerializer();

        foreach ($userValues as $paramName => $paramValue) {
            if (null === ($schema = $operation->getParam($paramName))) {
                continue;
            }

            switch ($schema->getLocation()) {
                case 'query':
                    $options['query'][$schema->getName()] = $paramValue;
                    break;
                case 'header':
                    $options['headers'] += self::parseHeader($schema, $paramName, $paramValue);
                    break;
                case 'json':
                    $json = isset($options['json']) ? $options['json'] : [];
                    $options['json'] = $jsonSerializer->stockJson($schema, $paramValue, $json);
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

    private static function parseHeader(Parameter $param, $name, $value)
    {
        if ($name == 'metadata' || $name == 'removeMetadata') {
            $headers = [];
            foreach ($value as $key => $keyVal) {
                $schema = $param->getItemSchema() ?: new Parameter(['prefix' => $param->getPrefix(), 'name' => $key]);
                $headers += self::parseHeader($schema, $key, $keyVal);
            }
            return $headers;
        }

        return is_string($value) || is_numeric($value)
            ? [$param->getPrefix() . $param->getName() => $value]
            : [];
    }
}
