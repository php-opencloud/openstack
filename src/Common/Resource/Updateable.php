<?php

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be updated.
 *
 * @package OpenStack\Common\Resource
 */
interface Updateable
{
    /**
     * Update the current resource with the configuration set out in the user options.
     *
     * @return void
     */
    public function update();
}
