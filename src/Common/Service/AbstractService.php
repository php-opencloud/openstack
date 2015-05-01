<?php

namespace OpenStack\Common\Service;

use OpenStack\Common\Api\Operator;

/**
 * Represents the top-level abstraction of a service.
 *
 * @package OpenStack\Common\Service
 */
abstract class AbstractService extends Operator implements ServiceInterface
{
    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    protected function getServiceNamespace()
    {
        return $this->getCurrentNamespace();
    }
}