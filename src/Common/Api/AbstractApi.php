<?php

namespace OpenStack\Common\Api;

abstract class AbstractApi implements ApiInterface
{
    protected function isRequired(array $param)
    {
        return array_merge($param, ['required' => true]);
    }

    protected function notRequired(array $param)
    {
        return array_merge($param, ['required' => false]);
    }
} 