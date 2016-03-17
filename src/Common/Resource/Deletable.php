<?php declare(strict_types=1);

namespace OpenCloud\Common\Resource;

/**
 * Represents a resource that can be deleted.
 *
 * @package OpenCloud\Common\Resource
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
