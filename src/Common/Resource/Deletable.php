<?php declare(strict_types=1);

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be deleted.
 *
 * @package OpenStack\Common\Resource
 */
interface Deletable
{
    /**
     * Permanently delete this resource.
     *
     * @return void
     */
    public function delete();
}
