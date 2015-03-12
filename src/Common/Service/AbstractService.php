<?php

namespace OpenStack\Common\Service;

use OpenStack\Common\Api\Operator;

abstract class AbstractService extends Operator implements ServiceInterface
{
    public function getServiceNamespace()
    {
        return $this->getCurrentNamespace();
    }
}