<?php

namespace OpenStack\Identity\v2\Models;

use OpenStack\Common\HydratorStrategyTrait;
use OpenStack\Common\Resource\AbstractResource;

/**
 * Represents an Identity v2 catalog entry endpoint.
 *
 * @package OpenStack\Identity\v2\Models
 */
class Endpoint extends AbstractResource
{
    use HydratorStrategyTrait;

    /** @var string */
    private $adminUrl;

    /** @var string */
    private $region;

    /** @var string */
    private $internalUrl;

    /** @var string */
    private $publicUrl;

    /**
     * {@inheritDoc}
     */
    public function populateFromArray(array $data)
    {
        $aliases = [
            'adminURL'    => 'adminUrl',
            'internalURL' => 'internalUrl',
            'publicURL'   => 'publicUrl',
        ];

        $this->hydrate($data, $aliases);
    }

    /**
     * Indicates whether a given region is supported
     *
     * @param string $region
     *
     * @return bool
     */
    public function supportsRegion($region)
    {
        return $this->region == $region;
    }

    /**
     * Indicates whether a given URL type is supported
     *
     * @param string $urlType
     *
     * @return bool
     */
    public function supportsUrlType($urlType)
    {
        $supported = false;

        switch (strtolower($urlType)) {
            case 'internalurl':
            case 'publicurl':
            case 'adminurl':
                $supported = true;
                break;
        }

        return $supported;
    }

    /**
     * Retrieves a URL for the endpoint based on a specific type.
     *
     * @param string $urlType Either "internalURL", "publicURL" or "adminURL" (case insensitive)
     *
     * @return bool|string
     */
    public function getUrl($urlType)
    {
        $url = false;

        switch (strtolower($urlType)) {
            case 'internalurl':
                $url = $this->internalUrl;
                break;
            case 'publicurl':
                $url = $this->publicUrl;
                break;
            case 'adminurl':
                $url = $this->adminUrl;
                break;
        }

        return $url;
    }
}