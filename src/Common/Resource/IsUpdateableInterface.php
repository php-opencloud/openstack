<?php

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be updated.
 *
 * @package OpenStack\Common\Resource
 */
interface IsUpdateableInterface
{
    /**
     * Update the current resource with the configuration set out in the user options.
     *
     * @param array $userOptions
     * @return void
     */
    public function update(array $userOptions);
}