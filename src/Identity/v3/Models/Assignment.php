<?php declare (strict_types=1);

namespace OpenStack\Identity\v3\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Listable;

class Assignment extends AbstractResource implements Listable
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
