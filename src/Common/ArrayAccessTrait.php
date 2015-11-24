<?php

namespace OpenStack\Common;

/**
 * Encapsulates common logic for classes which implement the SPL \ArrayAccess interface.
 *
 * @package OpenStack\Common
 */
trait ArrayAccessTrait
{
    /**
     * The internal state that this object represents
     *
     * @var array
     */
    private $internalState = [];

    /**
     * Sets an internal key with a value.
     *
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->internalState[] = $value;
        } else {
            $this->internalState[$offset] = $value;
        }
    }

    /**
     * Checks whether an internal key exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->internalState[$offset]);
    }

    /**
     * Unsets an internal key.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->internalState[$offset]);
    }

    /**
     * Retrieves an internal key.
     *
     * @param string $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->internalState[$offset] : null;
    }
}
