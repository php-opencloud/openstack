<?php

namespace OpenStack\Common\Resource;

/**
 * A resource that supports a GET or HEAD operation to retrieve more details.
 *
 * @package OpenStack\Common\Resource
 */
interface IsRetrievableInterface
{
    public function retrieve();
}