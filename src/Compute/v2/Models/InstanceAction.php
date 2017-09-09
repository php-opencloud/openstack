<?php declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class InstanceAction extends OperatorResource implements
    Listable
{

    /** @var string */
    public $requestId;

    /** @var string */
    public $action;

    /** @var string */
    public $instanceUuid;

    /** @var string */
    public $message;

    /** @var string */
    public $startTime;

    /** @var array */
    public $events;

    protected $resourceKey = 'instanceAction';
    protected $resourcesKey = 'instanceActions';

    protected $aliases = [
      'instance_uuid' => 'instanceUuid',
      'project_id' => 'projectId',
      'request_id' => 'requestId',
      'start_time' => 'startTime',
      'user_id' => 'userId'
    ];

}
