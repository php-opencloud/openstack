<?php declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasExtraSpecs;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents a Compute v2 Flavor.
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class Flavor extends OperatorResource implements Listable, Retrievable, Creatable, Deletable, HasExtraSpecs
{
    /** @var int */
    public $disk;

    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $ram;

    /** @var int */
    public $swap;

    /** @var int */
    public $vcpus;

    /** @var array */
    public $links;

    /** @var array */
    public $extraSpecs = [];

    protected $resourceKey = 'flavor';
    protected $resourcesKey = 'flavors';

    public function populateFromResponse(ResponseInterface $response): self
    {
        parent::populateFromResponse($response);
        $this->extraSpecs = $this->parseExtraSpecs($response);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getFlavor(), ['id' => (string)$this->id]);
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postFlavors(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteFlavor(), ['id' => (string)$this->id]);
    }

    public function getExtraSpecs(): array
    {
        $response = $this->executeWithState($this->api->getFlavorExtraSpecs());
        $this->extraSpecs = $this->parseExtraSpecs($response);
        return $this->extraSpecs;
    }

    public function mergeExtraSpecs(array $extraSpecs)
    {
        $this->execute($this->api->postFlavorExtraSpecs(), ['id' => $this->id, 'extraSpecs' => $extraSpecs]);
    }

    public function deleteExtraSpec($key)
    {
        $this->execute($this->api->deleteFlavorExtraSpecKey(), ['id' => $this->id, 'key' => $key]);
    }

    public function parseExtraSpecs(ResponseInterface $response): array
    {
        $json = Utils::jsonDecode($response);
        return isset($json['extra_specs']) ? $json['extra_specs'] : [];
    }
}
