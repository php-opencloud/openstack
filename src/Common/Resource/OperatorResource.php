<?php

namespace OpenStack\Common\Resource;

use OpenStack\Common\Api\Operator;

abstract class OperatorResource extends Operator implements ResourceInterface
{
    use ResourceTrait;
}