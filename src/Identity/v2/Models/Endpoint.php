<?php

namespace OpenStack\Identity\v2\Models;

use OpenStack\Common\HydratorStrategyTrait;
use OpenStack\Common\Resource\AbstractResource;

class Endpoint extends AbstractResource
{
    use HydratorStrategyTrait;

    private $adminUrl;
    private $region;
    private $internalUrl;
    private $publicUrl;

    public function populateFromArray(array $data)
    {
        $aliases = [
            'adminURL'    => 'adminUrl',
            'internalURL' => 'internalUrl',
            'publicURL'   => 'publicUrl',
        ];

        $this->hydrate($data, $aliases);
    }

    public function supportsRegion($region)
    {
        return $this->region == $region;
    }

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