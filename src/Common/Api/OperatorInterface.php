<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;
use OpenStack\Common\Error\Builder;

interface OperatorInterface
{
    public function __construct(ClientInterface $client);

    public function execute(array $definition, array $userOptions = []);
} 