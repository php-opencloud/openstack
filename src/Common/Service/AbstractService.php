<?php declare(strict_types=1);

namespace OpenStack\Common\Service;

use OpenStack\Common\Api\OperatorInterface;
use OpenStack\Common\Api\OperatorTrait;

/**
 * Represents the top-level abstraction of a service.
 *
 * @package OpenStack\Common\Service
 */
abstract class AbstractService implements ServiceInterface
{
    use OperatorTrait;
}
