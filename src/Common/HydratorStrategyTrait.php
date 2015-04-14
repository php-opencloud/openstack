<?php

namespace OpenStack\Common;

trait HydratorStrategyTrait
{
    private function hydrate(array $data)
    {
        foreach ($data as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }
}