<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\IsCreatableInterface;
use OpenStack\Common\Resource\IsDeletableInterface;
use OpenStack\Common\Resource\IsUpdateableInterface;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\ResourceInterface;

class Server extends OperatorResource implements ResourceInterface,
    IsCreatableInterface,
    IsUpdateableInterface,
    IsDeletableInterface
{
    public $aliases = [
        'block_device_mapping_v2' => 'blockDeviceMapping'
    ];

    public function create(array $userOptions)
    {
        $response = $this->execute('postServers', $userOptions);

        $this->fromResponse($response);

        return $this;
    }

    public function update(array $userOptions)
    {

    }

    public function delete()
    {

    }
}