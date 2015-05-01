<?php

namespace OpenStack\Common\Resource;

/**
 * A resource that supports a GET or HEAD operation to retrieve more details.
 *
 * @package OpenStack\Common\Resource
 */
interface IsRetrievable
{
    /**
     * Retrieve details of the current resource from the remote API.
     *
     * @return mixed
     */
    public function retrieve();
}