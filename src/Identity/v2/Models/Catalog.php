<?php

namespace OpenStack\Identity\v2\Models;

use GuzzleHttp\Message\ResponseInterface;
use OpenStack\Common\Resource\AbstractResource;

class Catalog extends AbstractResource
{
    const DEFAULT_URL_TYPE = 'publicURL';

    private $entries = [];

    public function fromResponse(ResponseInterface $response)
    {
        $entries = $response->json()['access']['serviceCatalog'];

        foreach ($entries as $entry) {
            $this->entries[] = $this->model('Entry', $entry);
        }
    }

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