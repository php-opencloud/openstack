<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operation;
use OpenStack\Common\Api\Operator;
use GuzzleHttp\Message\ResponseInterface;

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
     * @codeCoverageIgnore
     */
    protected function getServiceNamespace()
    {
        return str_replace('\\Models', '', $this->getCurrentNamespace());
    }

    /**
     * Internal method for flattening a nested array.
     *
     * @param array $data The nested array
     * @param null  $key  The key to extract
     *
     * @return array
     */
    private function flatten(array $data, $key = null)
    {
        $key = $key ?: $this->resourceKey;
        return $key && isset($data[$key]) ? $data[$key] : $data;
    }

    /**
     * Populates the current resource from a response object.
     *
     * @param ResponseInterface $response
     *
     * @return $this|ResourceInterface
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        $json = $response->json();

        if (!empty($json)) {
            $this->populateFromArray($this->flatten($json));
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
        foreach ($array as $key => $val) {
            $property = isset($this->aliases[$key]) ? $this->aliases[$key] : $key;
            if (property_exists($this, $property)) {
                $this->$property = $val;
            }
        }
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
     * This method iterates over a collection of resources. It sends the operation's request to the API,
     * parses the response, converts each element into {@see self} and - if pagination is supported - continues
     * to send requests until an empty collection is received back.
     *
     * For paginated collections, it sends subsequent requests according to a marker URL query. The value
     * of the marker will depend on the last element returned in the previous response. If a limit is
     * provided, the loop will continue up until that point.
     *
     * @param Operation $operation The operation responsible for retrieving a new collection
     * @param callable  $mapFn     An optional callback that will be executed on every resource iteration.
     */
    public function enumerate(Operation $operation, callable $mapFn = null)
    {
        $limit = $operation->getValue('limit') ?: false;
        $supportsPagination = $operation->hasParam('marker');
        $markerKey = $this->markerKey ?: self::DEFAULT_MARKER_KEY;

        $count = 0;
        $moreRequestsRequired = true;

        $totalReached = function ($count) use ($limit) {
            return $limit && $count >= $limit;
        };

        while ($moreRequestsRequired && $count < 20) {

            $response = $operation->send();
            $body = $response->json();
            $json = $this->flatten($body, $this->resourcesKey);

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
                    $operation->setValue('marker', $resource->$markerKey);
                }

                yield $resource;
            }

            if ($totalReached($count) || !$supportsPagination || empty($json)) {
                $moreRequestsRequired = false;
            }
        }
    }
}