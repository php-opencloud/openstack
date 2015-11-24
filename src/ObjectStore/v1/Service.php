<?php

namespace OpenStack\ObjectStore\v1;

use OpenStack\Common\Service\AbstractService;
use OpenStack\ObjectStore\v1\Models\Account;
use OpenStack\ObjectStore\v1\Models\Container;

/**
 * @property \OpenStack\ObjectStore\v1\Api $api
 */
class Service extends AbstractService
{
    /**
     * Retrieves an Account object.
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->model(Account::class);
    }

    /**
     * Retrieves a collection of container resources in a generator format.
     *
     * @param array         $options {@see \OpenStack\ObjectStore\v1\Api::getAccount}
     * @param callable|null $mapFn   Allows a function to be mapped over each element in the collection.
     *
     * @return \Generator
     */
    public function listContainers(array $options = [], callable $mapFn = null)
    {
        $options = array_merge($options, ['format' => 'json']);
        return $this->model(Container::class)->enumerate($this->api->getAccount(), $options, $mapFn);
    }

    /**
     * Retrieves a Container object and populates its name according to the value provided. Please note that the
     * remote API is not contacted.
     *
     * @param string $name The unique name of the container
     *
     * @return Container
     */
    public function getContainer($name = null)
    {
        return $this->model(Container::class, ['name' => $name]);
    }

    /**
     * Creates a new container according to the values provided.
     *
     * @param array $data {@see \OpenStack\ObjectStore\v1\Api::putContainer}
     *
     * @return Container
     */
    public function createContainer(array $data)
    {
        return $this->getContainer()->create($data);
    }
}
