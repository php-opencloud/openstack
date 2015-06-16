<?php

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\AbstractResource;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Role extends AbstractResource
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var array */
    public $links;
} 