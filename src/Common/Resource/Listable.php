<?php

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be enumerated (listed over).
 *
 * @package OpenStack\Common\Resource
 */
interface Listable
{
    /**
     * This method iterates over a collection of resources. It sends the operation's request to the API,
     * parses the response, converts each element into {@see self} and - if pagination is supported - continues
     * to send requests until an empty collection is received back.
     *
     * For paginated collections, it sends subsequent requests according to a marker URL query. The value
     * of the marker will depend on the last element returned in the previous response. If a limit is
     * provided, the loop will continue up until that point.
     *
     * @param array    $def       The operation definition
     * @param array    $userVals  The user values
     * @param callable $mapFn     An optional callback that will be executed on every resource iteration.
     *
     * @returns void
     */
    public function enumerate(array $def, array $userVals = [], callable $mapFn = null);
}