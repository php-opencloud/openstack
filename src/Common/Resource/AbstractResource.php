<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operator;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents a top-level abstraction of a remote API resource. Usually a resource represents a discrete
 * entity such as a Server, Container, Load Balancer. Apart from a representation of state, a resource can
 * also execute RESTFul operations on itself (updating, deleting, listing) or on other models.
 *
 * @package OpenStack\Common\Resource
 */
abstract class AbstractResource extends Operator implements ResourceInterface
{
    const DEFAULT_MARKER_KEY = 'id';

    /**
     * The JSON key that indicates how the API nests singular resources. For example, when
     * performing a GET, it could respond with ``{"server": {"id": "12345"}}``. In this case,
     * "server" is the resource key, since the essential state of the server is nested inside.
     *
     * @var string
     */
    protected $resourceKey;

    /**
     * The key that indicates how the API nests resource collections. For example, when
     * performing a GET, it could respond with ``{"servers": [{}, {}]}``. In this case, "servers"
     * is the resources key, since the array of servers is nested inside.
     *
     * @var string
     */
    protected $resourcesKey;

    /**
     * Indicates which attribute of the current resource should be used for pagination markers.
     *
     * @var string
     */
    protected $markerKey;

    /**
     * An array of aliases that will be checked when the resource is being populated. For example,
     *
     * 'FOO_BAR' => 'fooBar'
     *
     * will extract FOO_BAR from the response, and save it as 'fooBar' in the resource.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Populates the current resource from a response object.
     *
     * @param ResponseInterface $response
     *
     * @return $this|ResourceInterface
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
            $json = Utils::jsonDecode($response);
            if (!empty($json)) {
                $this->populateFromArray(Utils::flattenJson($json, $this->resourceKey));
            }
        }

        return $this;
    }

    /**
     * Populates the current resource from a data array.
     *
     * @param array $array
     *
     * @return mixed|void
     */
    public function populateFromArray(array $array)
    {
        $reflClass = new \ReflectionClass($this);

        foreach ($array as $key => $val) {
            $propertyName = isset($this->aliases[$key]) ? $this->aliases[$key] : $key;
            if (property_exists($this, $propertyName)) {
                if ($type = $this->extractTypeFromDocBlock($reflClass, $propertyName)) {
                    if (strpos($type, '[]') === 0 && is_array($val)) {
                        $array = [];
                        foreach ($val as $subVal) {
                            $array[] = $this->model($this->normalizeModelClass(substr($type, 2)), $subVal);
                        }
                        $val = $array;
                    } elseif (strcasecmp($type, '\datetimeimmutable') === 0) {
                        $val = new \DateTimeImmutable($val);
                    } elseif ($this->isNotNativeType($type)) {
                        $val = $this->model($this->normalizeModelClass($type), $val);
                    }
                }

                $this->$propertyName = $val;
            }
        }
    }

    private function isNotNativeType($type)
    {
        return !in_array($type, [
            'string', 'bool', 'boolean', 'null', 'array', 'object', 'int', 'integer', 'float', 'numeric', 'mixed'
        ]);
    }

    private function normalizeModelClass($class)
    {
        if (strpos($class, '\\') === false) {
            $currentNamespace = (new \ReflectionClass($this))->getNamespaceName();
            $class = sprintf("%s\\%s", $currentNamespace, $class);
        }

        return $class;
    }

    private function extractTypeFromDocBlock(\ReflectionClass $reflClass, $propertyName)
    {
        $docComment = $reflClass->getProperty($propertyName)->getDocComment();

        if (!$docComment) {
            return false;
        }

        $matches = [];
        preg_match('#@var ((\[\])?[\w|\\\]+)#', $docComment, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }

    /**
     * Internal method which retrieves the values of provided keys.
     *
     * @param array $keys
     *
     * @return array
     */
    protected function getAttrs(array $keys)
    {
        $output = [];

        foreach ($keys as $key) {
            if (property_exists($this, $key) && $this->$key !== null) {
                $output[$key] = $this->$key;
            }
        }

        return $output;
    }

    /**
     * @param array $definition
     *
     * @return mixed
     */
    public function executeWithState(array $definition)
    {
        return $this->execute($definition, $this->getAttrs(array_keys($definition['params'])));
    }

    /**
     * {@inheritDoc}
     */
    public function enumerate(array $def, array $userVals = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($def);
        $markerKey = $this->markerKey ?: self::DEFAULT_MARKER_KEY;
        $supportsPagination = $operation->hasParam('marker');

        $limit = isset($userVals['limit']) ? $userVals : false;
        $count = 0;

        $totalReached = function ($count) use ($limit) {
            return $limit && $count >= $limit;
        };

        while (true) {
            $response = $this->sendRequest($operation, $userVals);
            $json = Utils::jsonDecode($response);

            if (!$json) {
                break;
            }

            $json = Utils::flattenJson($json, $this->resourcesKey);

            if ($response->getStatusCode() === 204 || empty($json)) {
                break;
            }

            foreach ($json as $resourceData) {
                if ($totalReached($count)) {
                    break;
                }

                $count++;

                $resource = $this->newInstance();
                $resource->populateFromArray($resourceData);

                if ($mapFn) {
                    call_user_func_array($mapFn, [$resource]);
                }

                if ($supportsPagination) {
                    $userVals['marker'] = $resource->$markerKey;
                }

                yield $resource;
            }

            if ($totalReached($count) || !$supportsPagination) {
                break;
            }
        }
    }
}
