<?php

namespace OpenStack\Common;

/**
 * Encapsulates common logic for classes which implement the SPL \ArrayAccess interface.
 *
 * @package OpenStack\Common
 */
trait ArrayAccessTrait 
{
    private $internalState = [];

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->internalState[] = $value;
        } else {
            $this->internalState[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->internalState[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->internalState[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->internalState[$offset] : null;
    }
} 