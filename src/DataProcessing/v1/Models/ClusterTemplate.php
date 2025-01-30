<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;

class ClusterTemplate extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    public $neutronManagementNetwork;
    public $description;
    public $shares;
    public $clusterConfigs;
    public $createdAt;
    public $defaultImageId;
    public $updatedAt;
    public $pluginName;
    public $domainName;
    public $isDefault;
    public $isProtected;
    public $useAutoconfig;
    public $antiAffinity;
    public $tenantId;
    public $nodeGroups;
    public $isPublic;
    public $hadoopVersion;
    public $id;
    public $name;

    protected $resourceKey = 'cluster_template';
    protected $resourcesKey = 'cluster_templates';

    protected $aliases = [
        'neutron_management_network' => 'neutronManagementNetwork',
        'cluster_configs'            => 'clusterConfigs',
        'created_at'                 => 'createdAt',
        'default_image_id'           => 'defaultImageId',
        'updated_at'                 => 'updatedAt',
        'plugin_name'                => 'pluginName',
        'domain_name'                => 'domainName',
        'is_default'                 => 'isDefault',
        'is_protected'               => 'isProtected',
        'use_autoconfig'             => 'useAutoconfig',
        'anti_affinity'              => 'antiAffinity',
        'tenant_id'                  => 'tenantId',
        'node_groups'                => 'nodeGroups',
        'is_public'                  => 'isPublic',
        'hadoop_version'             => 'hadoopVersion',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getClusterTemplate(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postClusterTemplate(), $userOptions);

        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteClusterTemplate(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->putClusterTemplate(), $this->getAttrs(['id', 'name', 'pluginName', 'hadoopVersion', 'neutronManagementNetwork', 'description', 'shares', 'clusterConfigs', 'defaultImage', 'pluginName', 'domainName', 'isProtected', 'useAutoconfig', 'antiAffinity', 'nodeGroups', 'isPublic']));
        $this->populateFromResponse($response);
    }
}
