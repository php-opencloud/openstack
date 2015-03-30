<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Service\AbstractService;

class Service extends AbstractService
{
    /**
     * @param array $options
     *
     * @return Models\Server
     */
    public function createServer(array $options = [])
    {
        return $this->model('Server')->create($options);
    }
}