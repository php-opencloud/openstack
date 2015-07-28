<?php

namespace OpenStack\ObjectStore\v1;

use OpenStack\Common\Service\AbstractService;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Service extends AbstractService
{
    public function getAccount()
    {
        return $this->model('Account');
    }

    public function listContainers(array $options = [], callable $mapFn = null)
    {
        $options = array_merge($options, ['format' => 'json']);
        $operation = $this->getOperation($this->api->getAccount(), $options);
        return $this->model('Container')->enumerate($operation, $mapFn);
    }

    public function getContainer()
    {
        return $this->model('Container');
    }

    public function createContainer(array $data)
    {
        return $this->getContainer()->create($data);
    }
}