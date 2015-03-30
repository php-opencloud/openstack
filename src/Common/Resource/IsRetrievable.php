<?php

namespace OpenStack\Common\Resource;

/**
 * A resource that supports a GET or HEAD operation to retrieve more details.
 *
 * @package OpenStack\Common\Resource
 */
interface IsRetrievable
{
    public function retrieve();
}