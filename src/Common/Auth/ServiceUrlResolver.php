<?php

namespace OpenStack\Common\Auth;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use OpenStack\Identity\v2\Api;
use OpenStack\Identity\v2\Service as IdentityV2Service;

/**
 * Class responsible for parsing a KeyStone service catalog and retrieving the relevant base URL for
 * a service. This base URL is required for every HTTP client that the service and subsequent models
 * will use. It also parses the token and converts it into a usable resource model.
 *
 * @package OpenStack\Common\Auth
 */
class ServiceUrlResolver
{
    /**
     * The token extracted from the service catalog.
     *
     * @var \OpenStack\Identity\v2\Models\Token
     */
    private $token;

    /**
     * The base URL extracted from the service catalog.
     *
     * @var string
     */
    private $serviceUrl;

    /**
     * @var \OpenStack\Identity\v2\Service
     */
    private $identityService;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $options Configuration options:
     *
     *                       * catalogName (string) The name of the service as it appears in the catalog
     *                       * catalogType (string) The type of the service as it appears in the catalog
     *                       * region      (string) The region the service is situated in
     *                       * urlType     (string) The particular type of URL (publicURL, internalURL, etc.)
     *                       * debug       (bool)   Indicates whether a log subscriber should be attached to the
     *                                              HTTP client, allowing for helpful debugging.
     */
    public function resolve(array $options = [])
    {
        if (isset($options['debug']) && $options['debug'] === true) {
            $this->httpClient->getEmitter()->attach(new LogSubscriber(null, Formatter::DEBUG));
        }

        $api = new Api();
        $this->identityService = new IdentityV2Service($this->httpClient, $api);

        $authOpts = ['username' => null, 'password' => null, 'tenantId' => null, 'tenantName' => null];
        $response = $this->identityService->execute($api->postToken(), array_intersect_key($options, $authOpts));

        $this->serviceUrl = $this->identityService->model('Catalog', $response)->getEndpointUrl(
            $options['catalogName'],
            $options['catalogType'],
            $options['region'],
            $options['urlType']
        );

        $this->token = $this->identityService->model('Token', $response);
    }

    /**
     * Retrieves the token object
     *
     * @return \OpenStack\Identity\v2\Models\Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Retrieves the service URL
     *
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    /**
     * Retrieves the identity service
     *
     * @return \OpenStack\Identity\v2\Service
     */
    public function getService()
    {
        return $this->identityService;
    }
} 