<?php

namespace OpenStack\Common\Api;

use GuzzleHttp\ClientInterface;

interface OperatorInterface
{
    public function __construct(ClientInterface $client, ApiInterface $api);

    public function execute(array $definition, array $userOptions = []);

    public function model($name, $data = null);
}