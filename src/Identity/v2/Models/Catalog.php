<?php

namespace OpenStack\Identity\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents an Identity v2 service catalog.
 *
 * @package OpenStack\Identity\v2\Models
 */
class Catalog extends AbstractResource implements \OpenStack\Common\Auth\Catalog
{
    const DEFAULT_URL_TYPE = 'publicURL';

    /**
     * The catalog entries
     *
     * @var []Entry
     */
    public $entries = [];

    /**
     * {@inheritDoc}
     */
    public function populateFromResponse(ResponseInterface $response)
    {
        $entries = Utils::jsonDecode($response)['access']['serviceCatalog'];

        foreach ($entries as $entry) {
            $this->entries[] = $this->model(Entry::class, $entry);
        }
    }

    public function getServiceUrl($serviceName, $serviceType, $region, $urlType = self::DEFAULT_URL_TYPE)
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
