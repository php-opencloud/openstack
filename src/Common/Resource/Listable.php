<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operation;

/**
 * Represents a resource that can be enumerated (listed over).
 *
 * @package OpenStack\Common\Resource
 */
interface Listable
{
    /**
     * Lists over a collection of resources.
     *
     * @param Operation $operation The operation responsible for retrieving the next collection of
     *                             resources from the remote API.
     * @param callable  $mapFn     An anonymous function that will be executed on every iteration.
     *
     * @return \Generator A {@see \Traversable} collection of {@see self}
     */
    public function enumerate(Operation $operation, callable $mapFn = null);
}