<?php

namespace OpenStack\Common\Auth;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use OpenStack\Identity\v2\Api;
use OpenStack\Identity\v2\Service as IdentityV2Service;

class ServiceUrlResolver
{
    private $token;
    private $serviceUrl;
    private $identityService;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function resolve(array $options = [])
    {
        if (isset($options['debug']) && $options['debug'] === true) {
            $this->httpClient->getEmitter()->attach(new LogSubscriber(null, Formatter::DEBUG));
        }

        $this->identityService = new IdentityV2Service($this->httpClient);

        $authOpts = ['username' => null, 'password' => null, 'tenantId' => null, 'tenantName' => null];
        $response = $this->identityService->execute(Api::postToken(), array_intersect_key($options, $authOpts));

        $this->serviceUrl = $this->identityService->model('Catalog', $response)->getEndpointUrl(
            $options['catalogName'],
            $options['catalogType'],
            $options['region'],
            $options['urlType']
        );

        $this->token = $this->identityService->model('Token', $response);
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    public function getService()
    {
        return $this->identityService;
    }
} 