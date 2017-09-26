<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;

class NodeGroupTemplate extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    public $volumeLocalToInstance;
    public $availabilityZone;
    public $updatedAt;
    public $useAutoconfig;
    public $volumesPerNode;
    public $id;
    public $securityGroups;
    public $shares;
    public $nodeConfigs;
    public $autoSecurityGroup;
    public $volumesAvailabilityZone;
    public $description;
    public $volumeMountPrefix;
    public $pluginName;
    public $floatingIpPool;
    public $isDefault;
    public $imageId;
    public $volumesSize;
    public $isProxyGateway;
    public $isPublic;
    public $hadoopVersion;
    public $name;
    public $tenantId;
    public $createdAt;
    public $volumeType;
    public $isProtected;
    public $nodeProcesses;
    public $flavorId;

    protected $resourceKey = 'node_group_template';
    protected $resourcesKey = 'node_group_templates';

    protected $aliases = [
        'volume_local_to_instance'  => 'volumeLocalToInstance',
        'availability_zone'         => 'availabilityZone',
        'updated_at'                => 'updatedAt',
        'use_autoconfig'            => 'useAutoconfig',
        'volumes_per_node'          => 'volumesPerNode',
        'security_groups'           => 'securityGroups',
        'node_configs'              => 'nodeConfigs',
        'auto_security_group'       => 'autoSecurityGroup',
        'volumes_availability_zone' => 'volumesAvailabilityZone',
        'volume_mount_prefix'       => 'volumeMountPrefix',
        'plugin_name'               => 'pluginName',
        'floating_ip_pool'          => 'floatingIpPool',
        'is_default'                => 'isDefault',
        'image_id'                  => 'imageId',
        'volumes_size'              => 'volumesSize',
        'is_proxy_gateway'          => 'isProxyGateway',
        'is_public'                 => 'isPublic',
        'hadoop_version'            => 'hadoopVersion',
        'tenant_id'                 => 'tenantId',
        'created_at'                => 'createdAt',
        'volume_type'               => 'volumeType',
        'is_protected'              => 'isProtected',
        'node_processes'            => 'nodeProcesses',
        'flavor_id'                 => 'flavorId',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getNodeGroupTemplate(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postNodeGroupTemplate(), $userOptions);

        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteNodeGroupTemplate(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->putNodeGroupTemplate(), $this->getAttrs(['id', 'name', 'description', 'flavorId', 'availabilityZone', 'imageId', 'floatingIpPool', 'useAutoconfig', 'isProxyGateway', 'isPublic', 'isProtected']));
        $this->populateFromResponse($response);
    }
}
