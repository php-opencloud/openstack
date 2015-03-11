<?php

namespace OpenStack\Common\Resource;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Api\Operator;
use OpenStack\Common\ArrayAccessTrait;

abstract class OperatorResource extends Operator implements \ArrayAccess
{
    use ArrayAccessTrait;

    public function getServiceNamespace()
    {
        return str_replace('\\Models', '', $this->getCurrentNamespace());
    }

    public function fromResponse(ResponseInterface $response)
    {
        foreach ($response->json() + $response->getHeaders() as $key => $val) {
            $this->offsetSet(isset($this->aliases[$key]) ? $this->aliases[$key] : $key, $val);
        }

        return $this;
    }
}