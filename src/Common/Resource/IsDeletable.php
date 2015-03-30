<?php

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be deleted.
 *
 * @package OpenStack\Common\Resource
 */
interface IsDeletable
{
    /**
     * Permanently delete this resource.
     *
     * @return void
     */
    public function delete();
}