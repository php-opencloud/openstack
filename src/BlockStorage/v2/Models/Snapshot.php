<?php declare (strict_types=1);

namespace OpenStack\BlockStorage\v2\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\HasMetadata;
use OpenCloud\Common\Resource\HasWaiterTrait;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Retrievable;
use OpenCloud\Common\Resource\Updateable;
use OpenCloud\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * @property \OpenStack\BlockStorage\v2\Api $api
 */
class Snapshot extends AbstractResource implements Listable, Creatable, Updateable, Deletable, Retrievable, HasMetadata
{
    use HasWaiterTrait;

    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $status;

    /** @var string */
    public $description;

    /** @var \DateTimeImmutable */
    public $createdAt;

    /** @var array */
    public $metadata = [];

    /** @var string */
    public $volumeId;

    /** @var int */
    public $size;

    protected $resourceKey = 'snapshot';
    protected $resourcesKey = 'snapshots';
    protected $markerKey = 'id';

    protected $aliases = [
        'created_at' => 'createdAt',
        'volume_id'  => 'volumeId',
    ];

    public function populateFromResponse(ResponseInterface $response): self
    {
        parent::populateFromResponse($response);
        $this->metadata = $this->parseMetadata($response);
        return $this;
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getSnapshot());
        $this->populateFromResponse($response);
    }

    /**
     * @param array $userOptions {@see \OpenStack\BlockStorage\v2\Api::postSnapshots}
     *
     * @return Creatable
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postSnapshots(), $userOptions);
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $this->executeWithState($this->api->putSnapshot());
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteSnapshot());
    }

    public function getMetadata(): array
    {
        $response = $this->executeWithState($this->api->getSnapshotMetadata());
        $this->metadata = $this->parseMetadata($response);
        return $this->metadata;
    }

    public function mergeMetadata(array $metadata)
    {
        $this->getMetadata();
        $this->metadata = array_merge($this->metadata, $metadata);
        $this->executeWithState($this->api->putSnapshotMetadata());
    }

    public function resetMetadata(array $metadata)
    {
        $this->metadata = $metadata;
        $this->executeWithState($this->api->putSnapshotMetadata());
    }

    public function parseMetadata(ResponseInterface $response): array
    {
        $json = Utils::jsonDecode($response);
        return isset($json['metadata']) ? $json['metadata'] : [];
    }
}
