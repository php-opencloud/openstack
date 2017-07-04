<?php declare(strict_types=1);

namespace OpenStack\Metric\v1\Gnocchi\Models;

use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Metric\v1\Gnocchi\Api;

/**
 * @property Api $api
 */
class Metric extends OperatorResource implements Retrievable
{
    /** @var string */
    public $createdByUserId;

    /** @var \OpenStack\Metric\v1\Gnocchi\Models\Resource */
    public $resource;

    /** @var string */
    public $name;

    /** @var string */
    public $createdByProjectId;

    /** @var array */
    public $archivePolicy;

    /** @var string */
    public $id;

    /** @var string */
    public $unit;

    protected $aliases = [
        'created_by_user_id'    => 'createdByUserId',
        'created_by_project_id' => 'createdByProjectId',
        'archive_policy'        => 'archivePolicy',
    ];

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getMetric());
        $this->populateFromResponse($response);
    }
}
