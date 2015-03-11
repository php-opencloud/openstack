<?php

namespace OpenStack\Common\Resource;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\ArrayAccessTrait;

abstract class ValueResource implements \ArrayAccess, ResourceInterface
{
    use ArrayAccessTrait;

    public function getApiClass()
    {
        return sprintf("%s/Api", str_replace('Models\\', '', $this->reflClass()->getNamespaceName()));
    }

    public function fromResponse(ResponseInterface $response)
    {
        foreach ($response->json() + $response->getHeaders() as $key => $val) {
            $this->offsetSet(isset($this->aliases[$key]) ? $this->aliases[$key] : $key, $val);
        }

        return $this;
    }
} 