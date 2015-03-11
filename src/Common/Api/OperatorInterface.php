<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;

interface OperatorInterface
{
    public function __construct(ClientInterface $client);

    public function getServiceNamespace();

    public function getOperation($name, array $userOptions = []);
} 