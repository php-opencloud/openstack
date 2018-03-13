<?php

declare(strict_types=1);

namespace OpenStack\BlockStorage\v2\Models;

use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasMetadata;
use OpenStack\Common\Resource\HasWaiterTrait;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * @property \OpenStack\BlockStorage\v2\Api $api
 */
class Volume extends OperatorResource implements Creatable, Listable, Updateable, Deletable, Retrievable, HasMetadata
{
    use HasWaiterTrait;

    /** @var string */
    public $id;

    /** @var int */
    public $size;

    /** @var string */
    public $status;

    /** @var string */
    public $name;

    /** @var array */
    public $attachments;

    /** @var string */
    public $availabilityZone;

    /** @var \DateTimeImmutable */
    public $createdAt;

    /** @var string */
    public $description;

    /** @var string */
    public $volumeTypeName;

    /** @var string */
    public $snapshotId;

    /** @var string */
    public $sourceVolumeId;

    /** @var string */
    public $tenantId;

    /** @var string */
    public $host;

    /** @var array */
    public $metadata = [];

    /** @var array */
    public $volumeImageMetadata = [];

    protected $resourceKey  = 'volume';
    protected $resourcesKey = 'volumes';
    protected $markerKey    = 'id';

    protected $aliases = [
        'availability_zone'            => 'availabilityZone',
        'source_volid'                 => 'sourceVolumeId',
        'snapshot_id'                  => 'snapshotId',
        'volume_type'                  => 'volumeTypeName',
        'os-vol-tenant-attr:tenant_id' => 'tenantId',
        'os-vol-host-attr:host'        => 'host',
        'volume_image_metadata'        => 'volumeImageMetadata',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getAliases(): array
    {
        return parent::getAliases() + [
            'created_at' => new Alias('createdAt', \DateTimeImmutable::class),
        ];
    }

    public function populateFromResponse(ResponseInterface $response): self
    {
        parent::populateFromResponse($response);
        $this->metadata = $this->parseMetadata($response);

        return $this;
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getVolume());
        $this->populateFromResponse($response);
    }

    /**
     * @param array $userOptions {@see \OpenStack\BlockStorage\v2\Api::postVolumes}
     *
     * @return Creatable
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postVolumes(), $userOptions);

        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $response = $this->executeWithState($this->api->putVolume());
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteVolume());
    }

    public function getMetadata(): array
    {
        $response       = $this->executeWithState($this->api->getVolumeMetadata());
        $this->metadata = $this->parseMetadata($response);

        return $this->metadata;
    }

    public function mergeMetadata(array $metadata)
    {
        $this->getMetadata();
        $this->metadata = array_merge($this->metadata, $metadata);
        $this->executeWithState($this->api->putVolumeMetadata());
    }

    public function resetMetadata(array $metadata)
    {
        $this->metadata = $metadata;
        $this->executeWithState($this->api->putVolumeMetadata());
    }

    public function parseMetadata(ResponseInterface $response): array
    {
        $json = Utils::jsonDecode($response);

        return isset($json['metadata']) ? $json['metadata'] : [];
    }
}
