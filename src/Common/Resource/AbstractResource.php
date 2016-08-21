<?php declare(strict_types=1);

namespace OpenStack\Common\Resource;

use OpenStack\Common\Transport\Serializable;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents a top-level abstraction of a remote API resource. Usually a resource represents a discrete
 * entity such as a Server, Container, Load Balancer. Apart from a representation of state, a resource can
 * also execute RESTFul operations on itself (updating, deleting, listing) or on other models.
 *
 * @package OpenStack\Common\Resource
 */
abstract class AbstractResource implements ResourceInterface, Serializable
{
    /**
     * The JSON key that indicates how the API nests singular resources. For example, when
     * performing a GET, it could respond with ``{"server": {"id": "12345"}}``. In this case,
     * "server" is the resource key, since the essential state of the server is nested inside.
     *
     * @var string
     */
    protected $resourceKey;

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
     * @return AbstractResource
     */
    public function populateFromResponse(ResponseInterface $response): self
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
    public function populateFromArray(array $array): self
    {
        $reflClass = new \ReflectionClass($this);

        foreach ($array as $key => $val) {
            $propertyName = (string) (isset($this->aliases[$key]) ? $this->aliases[$key] : $key);

            if (property_exists($this, $propertyName)) {
                if ($type = $this->extractTypeFromDocBlock($reflClass, $propertyName)) {
                    $val = $this->parseDocBlockValue($type, $val);
                }

                $this->$propertyName = $val;
            }
        }

        return $this;
    }

    private function parseDocBlockValue(string $type, $val)
    {
        if (is_null($val)) {
            return $val;
        } elseif (strpos($type, '[]') === 0 && is_array($val)) {
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

    private function isNotNativeType(string $type): bool
    {
        return !in_array($type, [
            'string', 'bool', 'boolean', 'double', 'null', 'array', 'object', 'int', 'integer', 'float', 'numeric',
            'mixed'
        ]);
    }

    private function normalizeModelClass(string $class): string
    {
        if (strpos($class, '\\') === false) {
            $currentNamespace = (new \ReflectionClass($this))->getNamespaceName();
            $class = sprintf("%s\\%s", $currentNamespace, $class);
        }

        return $class;
    }

    private function extractTypeFromDocBlock(\ReflectionClass $reflClass, string $propertyName)
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

    public function model(string $class, $data = null): ResourceInterface
    {
        $model = new $class();

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

    public function serialize(): \stdClass
    {
        $output = new \stdClass();

        foreach ((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $val = $this->{$name};

            $fn = function ($val) {
                return ($val instanceof Serializable) ? $val->serialize() : $val;
            };

            if (is_array($val)) {
                foreach ($val as $sk => $sv) {
                    $val[$sk] = $fn($sv);
                }
            }

            $output->{$name} = $fn($val);
        }

        return $output;
    }
}
