<?php declare (strict_types=1);

namespace OpenStack\Identity\v3\Models;

use OpenCloud\Common\Resource\OperatorResource;
use OpenCloud\Common\Resource\Listable;

class Assignment extends OperatorResource implements Listable
{
    /** @var Role */
    public $role;

    /** @var \stdClass */
    public $scope;

    /** @var Group */
    public $group;

    /** @var User */
    public $user;
}
