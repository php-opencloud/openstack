<?php declare(strict_types=1);

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
     * @param array $userOptions
     * @return self
     */
    public function update(array $userOptions): Updateable;
}
