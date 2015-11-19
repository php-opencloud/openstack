<?php

namespace OpenStack\BlockStorage\v2\Models;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasMetadata;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Updateable;
use OpenStack\Common\Transport\Utils;
use OpenStack\ObjectStore\v1\Models\MetadataTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * @property \OpenStack\BlockStorage\v2\Api $api
 */
class Snapshot extends AbstractResource implements Listable, Creatable, Updateable, Deletable, HasMetadata
{
    use MetadataTrait;

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
    public $metadata;

    /** @var string */
    public $volumeId;

    /** @var int */
    public $size;

    protected $resourceKey = 'snapshot';
    protected $resourcesKey = 'snapshots';

    protected $aliases = [
        'created_at' => 'createdAt',
        'volume_id'  => 'volumeId',
    ];

    public function populateFromResponse(ResponseInterface $response)
    {
        parent::populateFromResponse($response);

        $this->metadata = $this->parseMetadata($response);

        return $this;
    }

    /**
     * @param array $userOptions {@see \OpenStack\BlockStorage\v2\Api::postSnapshots}
     *
     * @return self
     */
    public function create(array $userOptions)
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

    public function getMetadata()
    {
        $response = $this->executeWithState($this->api->getSnapshotMetadata());
        return $this->parseMetadata($response);
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
        return isset($json['metadata']) ? $json['metadata'] : null;
    }
}