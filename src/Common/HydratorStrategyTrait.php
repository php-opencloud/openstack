<?php

namespace OpenStack\Common;

trait HydratorStrategyTrait
{
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