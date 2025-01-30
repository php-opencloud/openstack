<?php

declare(strict_types=1);

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Catalog extends OperatorResource implements \OpenStack\Common\Auth\Catalog
{
    /** @var []Service */
    public $services;

    protected function getAliases(): array
    {
        return parent::getAliases() + [
            'services' => new Alias('services', Service::class, true),
        ];
    }

    public function populateFromArray(array $data): self
    {
        foreach ($data as $service) {
            $this->services[] = $this->model(Service::class, $service);
        }

        return $this;
    }

    /**
     * Override a given service's predetermined endpoint URL.
     *
     * @param string $name      the name of the service as it appears in the catalog
     * @param string $type      the type of the service as it appears in the catalog
     * @param string $region    the region of the service as it appears in the catalog
     * @param string $interface the interface of the service as it appears in the catalog
     *
     * @return null|string NULL if no URL found
     */
    public function getServiceUrlOverride(
        string $name,
        string $type,
        string $region,
        string $interface,
        array $overrides
    ): ?string {
        foreach ($overrides as $override) {
            if (
                (empty($override['name']) || $name == $override['name'])
                && (empty($override['type']) || $type == $override['type'])
                && (empty($override['region']) || $region == $override['region'])
                && (empty($override['interface']) || $interface == $override['interface'])
            ) {
                if (empty($override['name']) && empty($override['type'])) {
                    throw new \RuntimeException(sprintf("Endpoint override must at least specify an \"url\" and either \"name\" or a \"type\"."));
                }
                if (empty($override['url'])) {
                    throw new \RuntimeException(sprintf("Endpoint override must specify an \"url\".\nName: %s\nType: %s\nRegion: %s\nInterface: %s", $override['name'] ?? '', $override['type'] ?? '', $override['region'] ?? '', $override['interface'] ?? ''));
                }

                return $override['url'];
            }
        }

        return null;
    }

    /**
     * Retrieve a base URL for a service, according to its catalog name, type, region.
     *
     * @param string $name    the name of the service as it appears in the catalog
     * @param string $type    the type of the service as it appears in the catalog
     * @param string $region  the region of the service as it appears in the catalog
     * @param string $urlType unused
     *
     * @return false|string FALSE if no URL found
     */
    public function getServiceUrl(string $name, string $type, string $region, string $urlType): string
    {
        if (empty($this->services)) {
            throw new \RuntimeException('No services are defined');
        }

        foreach ($this->services as $service) {
            if (false !== ($url = $service->getUrl($name, $type, $region, $urlType))) {
                return $url;
            }
        }

        throw new \RuntimeException(sprintf("Endpoint URL could not be found in the catalog for this service.\nName: %s\nType: %s\nRegion: %s\nURL type: %s", $name, $type, $region, $urlType));
    }
}
