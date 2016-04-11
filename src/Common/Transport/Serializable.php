<?php

namespace OpenCloud\Common\Transport;

interface Serializable
{
    /**
     * @return string
     */
    public function serialize(): \stdClass;
}
