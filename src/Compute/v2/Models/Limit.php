<?php declare (strict_types = 1);

namespace OpenStack\Compute\v2\Models;

use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\OperatorResource;
use OpenCloud\Common\Resource\Retrievable;
use OpenCloud\Common\Transport\Utils;

/**
 * Represents a Compute v2 Quota
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class Limit extends OperatorResource
{
    /** @var string */
    public $rate;

    /** @var object */
    public $absolute;

    protected $resourceKey = 'limits';
}
