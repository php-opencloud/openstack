<?php declare(strict_types=1);

namespace OpenCloud\Common\Service;

use OpenCloud\Common\Api\OperatorInterface;
use OpenCloud\Common\Api\OperatorTrait;

/**
 * Represents the top-level abstraction of a service.
 *
 * @package OpenCloud\Common\Service
 */
abstract class AbstractService implements ServiceInterface
{
    use OperatorTrait;
}
