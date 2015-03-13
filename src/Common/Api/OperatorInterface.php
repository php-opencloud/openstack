<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;

interface OperatorInterface
{
    public function __construct(ClientInterface $client);

    public function getOperation(array $definition, array $userOptions = []);
} 