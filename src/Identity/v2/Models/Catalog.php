<?php

namespace OpenStack\Identity\v2\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;

/**
 * Represents an Identity v2 service catalog.
 *
 * @package OpenStack\Identity\v2\Models
 */
class Catalog extends AbstractResource
{
    const DEFAULT_URL_TYPE = 'publicURL';

    /**
     * The catalog entries
     *
     * @var []Entry
     */
    private $entries = [];

    /**
     * {@inheritDoc}
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        $entries = $response->json()['access']['serviceCatalog'];

        foreach ($entries as $entry) {
            $this->entries[] = $this->model('Entry', $entry);
        }
    }

    /**
     * Attempts to retrieve the base URL for a service from the catalog according to the arguments provided.
     *
     * @param string $serviceName The name of the service as it appears in the catalog
     * @param string $serviceType The type of the service as it appears in the catalog
     * @param string $region      The region of the service as it appears in the catalog
     * @param string $urlType     The URL type of the service as it appears in the catalog
     */
    public function getEndpointUrl($serviceName, $serviceType, $region, $urlType = self::DEFAULT_URL_TYPE)
    {
        foreach ($this->entries as $entry) {
            if ($entry->matches($serviceName, $serviceType) && ($url = $entry->getEndpointUrl($region, $urlType))) {
                return $url;
            }
        }

        throw new \RuntimeException(sprintf(
            "Endpoint URL could not be found in the catalog for this service.\nName: %s\nType: %s\nRegion: %s\nURL type: %s",
            $serviceName, $serviceType, $region, $urlType
        ));
    }
}