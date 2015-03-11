<?php

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be deleted.
 *
 * @package OpenStack\Common\Resource
 */
interface IsDeletableInterface
{
    /**
     * Permanently delete this resource.
     *
     * @return void
     */
    public function delete();
}