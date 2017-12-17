<?php declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Aggregate extends OperatorResource implements
    Retrievable,
    Listable
{
    /** @var int */
    public $id;

    /** @var string */
    public $availabiltyZone;

    /** @var string */
    public $createdAt;

    /** @var string */
    public $deletedAt;

    /** @var boolean */
    public $deleted;

    /** @var array */
    public $hosts;

    /** @var array */
    public $metadata;

    /** @var string */
    public $name;

    /** @var string */
    public $updatedAt;

    /** @var string */
    public $uuid;

    protected $resourceKey = 'aggregate';
    protected $resourcesKey = 'aggregates';

    protected $aliases = [
      'availability_zone' => 'availabilityZone',
      'created_at'        => 'createdAt',
      'deleted_at'        => 'deletedAt',
      'updated_at'        => 'updatedAt'
    ];

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getAggregate(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }
}
