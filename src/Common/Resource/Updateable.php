<?php declare(strict_types=1);

namespace OpenCloud\Common\Resource;

/**
 * Represents a resource that can be updated.
 *
 * @package OpenCloud\Common\Resource
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
