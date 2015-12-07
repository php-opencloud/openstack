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
                    $val = $this->parseDocBlockValue($type, $val);
                }

                $this->$propertyName = $val;
            }
        }
    }

    private function parseDocBlockValue($type, $val)
    {
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

        return $val;
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

    private function getResourcesKey()
    {
        $resourcesKey = $this->resourcesKey;

        if (!$resourcesKey) {
            $class = substr(static::class, strrpos(static::class, '\\') + 1);
            $resourcesKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class)) . 's';
        }

        return $resourcesKey;
    }

    /**
     * {@inheritDoc}
     */
    public function enumerate(array $def, array $userVals = [], callable $mapFn = null)
    {
        $operation = $this->getOperation($def);

        $requestFn = function ($marker) use ($operation, $userVals) {
            if ($marker) {
                $userVals['marker'] = $marker;
            }
            return $this->sendRequest($operation, $userVals);
        };

        $resourceFn = function (array $data) {
            $resource = $this->newInstance();
            $resource->populateFromArray($data);
            return $resource;
        };

        $opts = [
            'limit'        => isset($userVals['limit']) ? $userVals['limit'] : null,
            'resourcesKey' => $this->getResourcesKey(),
            'markerKey'    => $this->markerKey,
            'mapFn'        => $mapFn,
        ];

        $iterator = new Iterator($opts, $requestFn, $resourceFn);
        return $iterator();
    }

    public function extractMultipleInstances(ResponseInterface $response, $key = null)
    {
        $key = $key ?: $this->getResourcesKey();
        $resourcesData = Utils::jsonDecode($response)[$key];

        $resources = [];

        foreach ($resourcesData as $resourceData) {
            $resource = $this->newInstance();
            $resource->populateFromArray($resourceData);
            $resources[] = $resource;
        }

        return $resources;
    }
}