<?php

declare(strict_types=1);

namespace OpenStack\BlockStorage\v3\Models;

use OpenStack\BlockStorage\v3\Api;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property Api $api
 */
class VolumeAttachment extends OperatorResource implements Listable
{
    /** @var string */
    public $id;

    /** @var int */
    public $device;

    /** @var string */
    public $serverId;

    /** @var string */
    public $volumeId;

    protected $resourceKey  = 'volumeAttachment';
    protected $resourcesKey = 'volumeAttachments';
}
