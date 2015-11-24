<?php

namespace OpenStack\Common;

/**
 * Represents common functionality for populating, or "hydrating", an object with arbitrary data.
 *
 * @package OpenStack\Common
 */
trait HydratorStrategyTrait
{
    /**
     * Hydrates an object with set data
     *
     * @param array $data    The data to set
     * @param array $aliases Any aliases
     */
    private function hydrate(array $data, array $aliases = [])
    {
        foreach ($data as $key => $val) {
            $key = isset($aliases[$key]) ? $aliases[$key] : $key;
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }
}
