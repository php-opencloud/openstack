<?php

declare(strict_types=1);

namespace OpenStack\BlockStorage\v3\Models;

use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasMetadata;
use OpenStack\Common\Resource\HasWaiterTrait;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;
use OpenStack\Common\Transport\Utils;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents a BlockStorage v3 Quota Set.
 *
 * @property \OpenStack\BlockStorage\v3\Api $api
 */

class Backup extends OperatorResource implements Creatable
{
    use HasWaiterTrait;

    /** @var string */
    public $id;

    /** @var string */
    public $status;

    /** @var string */
    public $name;

    /** @var string */
    public $availabilityZone;

    /** @var \DateTimeImmutable */
    public $createdAt;

    /** @var string */
    public $description;

    /** @var string */
    public $snapshotId;

    /** @var string */
    public $tenantId;

    /** @var array */
    public $metadata = [];

    public $size;

    public $volumeId;

    public $userId;

    protected $resourceKey  = 'backup';
    protected $resourcesKey = 'backups';
    protected $markerKey    = 'id';

    protected $aliases = [
        'availability_zone'            => 'availabilityZone',
        'source_volid'                 => 'sourceVolumeId',
        'snapshot_id'                  => 'snapshotId',
        'volume_id'                    => 'volumeId',
        'user_id'                      => 'userId',
        'volume_type'                  => 'volumeTypeName',
        'os-vol-tenant-attr:tenant_id' => 'tenantId',
        'os-vol-host-attr:host'        => 'host',
        'volume_image_metadata'        => 'volumeImageMetadata',
    ];

    protected function getAliases(): array
    {
        return parent::getAliases() + [
                'created_at' => new Alias('createdAt', \DateTimeImmutable::class),
            ];
    }

    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postBackups(), $userOptions);
        return $this->populateFromResponse($response);
    }

    public function update()
    {
        $response = $this->executeWithState($this->api->putBackup());
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteBackup());
    }

    public function restore(array $userOptions)
    {
        $response = $this->execute($this->api->restoreBackup(), $userOptions);
        return $this->populateFromResponse($response);
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getBackup());
        $this->populateFromResponse($response);
    }
}