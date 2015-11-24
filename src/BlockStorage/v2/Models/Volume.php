<?php
namespace OpenStack\BlockStorage\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
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
class Volume extends AbstractResource implements Creatable, Listable, Updateable, Deletable, Retrievable, HasMetadata
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

    /** @var array */
    public $metadata = [];

    protected $resourceKey = 'volume';
    protected $resourcesKey = 'volumes';
    protected $aliases = [
        'availability_zone' => 'availabilityZone',
        'source_volid'      => 'sourceVolumeId',
        'snapshot_id'       => 'snapshotId',
        'created_at'        => 'createdAt',
        'volume_type'       => 'volumeTypeName',
    ];

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);
        $this->metadata = $this->parseMetadata($response);
        return $this;
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getVolume());
        return $this->populateFromResponse($response);
    }

    /**
     * @param array $userOptions {@see \OpenStack\BlockStorage\v2\Api::postVolumes}
     *
     * @return self
     */
    public function create(array $userOptions)
    {
        $response = $this->execute($this->api->postVolumes(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * @return self
     */
    public function update()
    {
        $response = $this->executeWithState($this->api->putVolume());
        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteVolume());
    }

    public function getMetadata()
    {
    }

    public function mergeMetadata(array $metadata)
    {
    }

    public function resetMetadata(array $metadata)
    {
    }

    public function parseMetadata(ResponseInterface $response)
    {
        $json = Utils::jsonDecode($response);
        return isset($json['metadata']) ? $json['metadata'] : [];
    }
}
