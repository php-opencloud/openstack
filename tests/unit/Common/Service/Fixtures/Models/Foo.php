<?php

namespace OpenCloud\Test\Common\Service\Fixtures\Models;

use OpenCloud\Common\Resource\OperatorResource;

class Foo extends OperatorResource
{
    public function testGetService()
    {
        return $this->getService();
    }
}
