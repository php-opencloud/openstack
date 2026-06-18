<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;

class NodeGroup extends OperatorResource implements Listable
{
    public $nodeGroups;

    protected $resourceKey = 'cluster';
    protected $resourcesKey = 'clusters';

    protected $aliases = [
        'node_groups' => 'nodeGroups',
    ];
}
