<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

class Cluster extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    public $isTransient;
    public $userKeypairId;
    public $updatedAt;
    public $provisionProgress;
    public $useAutoconfig;
    public $nodeGroups;
    public $managementPublicKey;
    public $id;
    public $trustId;
    public $clusterConfigs;
    public $defaultImageId;
    public $domainName;
    public $shares;
    public $status;
    public $neutronManagementNetwork;
    public $description;
    public $pluginName;
    public $antiAffinity;
    public $isPublic;
    public $statusDescription;
    public $hadoopVersion;
    public $info;
    public $clusterTemplateId;
    public $name;
    public $tenantId;
    public $createdAt;
    public $isProtected;
    public $verification;

    protected $resourceKey = 'cluster';
    protected $resourcesKey = 'clusters';

    protected $aliases = [
            'is_transient'               => 'isTransient',
            'user_keypair_id'            => 'userKeypairId',
            'updated_at'                 => 'updatedAt',
            'provision_progress'         => 'provisionProgress',
            'use_autoconfig'             => 'useAutoconfig',
            'node_groups'                => 'nodeGroups',
            'management_public_key'      => 'managementPublicKey',
            'trust_id'                   => 'trustId',
            'cluster_configs'            => 'clusterConfigs',
            'default_image_id'           => 'defaultImageId',
            'domain_name'                => 'domainName',
            'neutron_management_network' => 'neutronManagementNetwork',
            'plugin_name'                => 'pluginName',
            'anti_affinity'              => 'antiAffinity',
            'is_public'                  => 'isPublic',
            'status_description'         => 'statusDescription',
            'hadoop_version'             => 'hadoopVersion',
            'cluster_template_id'        => 'clusterTemplateId',
            'tenant_id'                  => 'tenantId',
            'created_at'                 => 'createdAt',
            'is_protected'               => 'isProtected',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getCluster(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postCluster(), $userOptions);
        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteCluster(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->patchCluster(), $this->getAttrs(['id', 'name', 'isPublic', 'isProtected', 'description']));
        $this->populateFromResponse($response);
    }

    public function scale(array $userOptions)
    {
        $response = $this->execute($this->api->putCluster(), array_merge($this->getAttrs(['id']), $userOptions));

        return $this->populateFromResponse($response);
    }

    public function getNodeGroups(array $options = []): array
    {
        $response = $this->execute($this->api->getNodeGroups(), $options);

        return Utils::jsonDecode($response);
    }
}
