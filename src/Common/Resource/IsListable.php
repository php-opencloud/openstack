<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operation;

interface IsListable
{
    public function enumerate(Operation $operation, callable $mapFn = null);
}