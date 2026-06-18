<?php

namespace OpenStack\DataProcessing\v1;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params();
    }

    public function getClusters(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'clusters',
            'params' => [
                'limit'   => $this->params->limit(),
                'marker'  => $this->params->marker(),
                'sortBy' => $this->params->sortBy(),
            ],
        ];
    }

    public function getCluster(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'clusters/{id}',
            'params' => [
                'id'     => $this->params->urlId('cluster')
            ],
        ];
    }

    public function deleteCluster(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'clusters/{id}',
            'params' => ['id' => $this->params->urlId('cluster')],
        ];
    }

    public function patchCluster(): array
    {
        return [
            'method' => 'PATCH',
            'path'   => 'clusters/{id}',
            'params' => [
                'id'          => $this->params->urlId('cluster'),
                'isPublic'    => $this->params->isPublic(),
                'name'        => $this->params->name('cluster'),
                'isProtected' => $this->params->isProtected(),
                'description' => $this->params->description(),
            ],
        ];
    }

    public function postCluster(): array
    {
        return [
            'path'   => 'clusters',
            'method' => 'POST',
            'params' => [
                'pluginName'               => $this->params->pluginName(),
                'hadoopVersion'            => $this->params->hadoopVersion(),
                'clusterTemplateId'        => $this->params->clusterTemplateId(),
                'defaultImageId'           => $this->params->defaultImageId(),
                'userKeypairId'            => $this->notRequired($this->params->userKeyPairId()),
                'name'                     => $this->isRequired($this->params->name('cluster')),
                'neutronManagementNetwork' => $this->params->neutronManagementNetwork(),
                'description'              => $this->params->description(),
                'isPublic'                 => $this->params->isPublic(),
                'isProtected'              => $this->params->isProtected(),
            ],
        ];
    }

    public function postClusters(): array
    {
        $definition = $this->postCluster();
        $definition['path'] .= '/multiple';
        $definition['params'] = array_merge($definition['params'], [
            'count'          => $this->params->count(),
            'clusterConfigs' => $this->params->clusterConfigs(),
        ]);

        return $definition;
    }

    public function putCluster(): array
    {
        return [
            'path'   => 'clusters/{id}',
            'method' => 'PUT',
            'params' => [
                'id'               => $this->params->urlId('cluster'),
                'addNodeGroups'    => $this->params->addNodeGroups(),
                'resizeNodeGroups' => $this->params->resizeNodeGroups(),
            ],
        ];
    }

    public function postDataSource(): array
    {
        return [
            'path'   => 'data-sources',
            'method' => 'POST',
            'params' => [
                'description' => $this->params->description(),
                'url'         => $this->params->url(),
                'type'        => $this->params->dataSourceType(),
                'name'        => $this->params->dataSourceName(),
            ],
        ];
    }

    public function deleteDataSource(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'data-sources/{id}',
            'params' => ['id' => $this->params->urlId('datasource')],
        ];
    }

    public function getDataSource(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'data-sources/{id}',
            'params' => [
                'id' => $this->params->urlId('datasource'),
            ],
        ];
    }

    public function getDataSources(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'data-sources',
            'params' => [
                'limit'   => $this->params->limit(),
                'marker'  => $this->params->marker(),
                'sortBy' => $this->params->sortBy(),
            ],
        ];
    }

    public function putDataSource(): array
    {
        return [
            'method' => 'PUT',
            'path'   => 'data-sources/{id}',
            'params' => [
                'id'          => $this->params->urlId('datasource'),
                'isPublic'    => $this->params->isPublic(),
                'isProtected' => $this->params->isProtected(),
                'name'        => $this->notRequired($this->params->dataSourceName()),
                'description' => $this->params->description(),
                'url'         => $this->notRequired($this->params->url()),
                'type'        => $this->notRequired($this->params->dataSourceType()),
            ],
        ];
    }

    public function postClusterTemplate(): array
    {
        return [
            'path'   => 'cluster-templates',
            'method' => 'POST',
            'params' => [
                'pluginName'               => $this->params->pluginName(),
                'hadoopVersion'            => $this->params->hadoopVersion(),
                'name'                     => $this->isRequired($this->params->name('cluster-template')),
                'nodeGroups'               => $this->params->nodeGroups(),
                'neutronManagementNetwork' => $this->notRequired($this->params->neutronManagementNetwork()),
                'description'              => $this->params->description(),
                'shares'                   => $this->params->shares(),
                'clusterConfigs'           => $this->params->clusterConfigs(),
                'defaultImageId'           => $this->notRequired($this->params->defaultImageId()),
                'domainName'               => $this->params->domainName(),
                'isProtected'              => $this->params->isProtected(),
                'useAutoconfig'            => $this->params->useAutoconfig(),
                'antiAffinity'             => $this->params->antiAffinity(),
                'isPublic'                 => $this->params->isPublic(),
            ],
        ];
    }

    public function getClusterTemplates(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'cluster-templates',
            'params' => [
                'limit'  => $this->params->limit(),
                'marker' => $this->params->marker(),
                'sortBy' => $this->params->sortkey(),
            ],
        ];
    }

    public function getClusterTemplate(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'cluster-templates/{id}',
            'params' => [
                'id' => $this->params->urlId('cluster-templates'),
            ],
        ];
    }

    public function deleteClusterTemplate(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'cluster-templates/{id}',
            'params' => ['id' => $this->params->urlId('cluster-template')],
        ];
    }

    public function putClusterTemplate(): array
    {
        return [
            'method' => 'PUT',
            'path'   => 'cluster-templates/{id}',
            'params' => [
                'id' => $this->params->urlId('cluster-template'),
                'nodeGroups'               => $this->params->nodeGroups(),
                'neutronManagementNetwork' => $this->notRequired($this->params->neutronManagementNetwork()),
                'description'              => $this->params->description(),
                'shares'                   => $this->params->shares(),
                'clusterConfigs'           => $this->params->clusterConfigs(),
                'defaultImageId'           => $this->notRequired($this->params->defaultImageId()),
                'pluginName'               => $this->notRequired($this->params->pluginName()),
                'domainName'               => $this->params->domainName(),
                'isProtected'              => $this->params->isProtected(),
                'useAutoconfig'            => $this->params->useAutoconfig(),
                'antiAffinity'             => $this->params->antiAffinity(),
                'isPublic'                 => $this->params->isPublic(),
                'hadoopVersion'            => $this->notRequired($this->params->hadoopVersion()),
                'name'                     => $this->params->name('cluster-template'),
            ],
        ];
    }

    public function getNodeGroupTemplates()
    {
        return [
            'method' => 'GET',
            'path'   => 'node-group-templates',
            'params' => [
                'limit'   => $this->params->limit(),
                'marker'  => $this->params->marker(),
                'sortBy' => $this->params->sortBy(),
            ],
        ];
    }

    public function postNodeGroupTemplate()
    {
        return [
            'path'   => 'node-group-templates',
            'method' => 'POST',
            'params' => [
                'pluginName'       => $this->params->pluginName(),
                'hadoopVersion'    => $this->params->hadoopVersion(),
                'nodeProcesses'    => $this->params->nodeProcesses(),
                'name'             => $this->isRequired($this->params->name('nodeGroupTemplate')),
                'flavorId'         => $this->params->flavorId(),
                'description'      => $this->params->description(),
                'availabilityZone' => $this->params->availabilityZone(),
                'imageId'          => $this->params->imageId(),
                'floatingIpPool'   => $this->params->floatingIpPool(),
                'useAutoconfig'    => $this->params->useAutoconfig(),
                'autoSecurityGroup'    => $this->params->autoSecurityGroup(),
                'isProxyGateway'   => $this->params->isProxyGateway(),
                'isPublic'         => $this->params->isPublic(),
                'isProtected'      => $this->params->isProtected(),
            ],
        ];
    }

    public function getNodeGroupTemplate()
    {
        return [
            'method' => 'GET',
            'path'   => 'node-group-templates/{id}',
            'params' => [
                'id' => $this->params->urlId('nodeGroupTemplate'),
            ],
        ];
    }

    public function deleteNodeGroupTemplate()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'node-group-templates/{id}',
            'params' => ['id' => $this->params->urlId('nodeGroupTemplate')],
        ];
    }

    public function putNodeGroupTemplate()
    {
        return [
            'method' => 'PUT',
            'path'   => 'node-group-templates/{id}',
            'params' => [
                'id'               => $this->params->urlId('nodeGroupTemplate'),
                'name'             => $this->params->name('nodeGroupTemplate'),
                'description'      => $this->params->description(),
                'availabilityZone' => $this->params->availabilityZone(),
                'imageId'          => $this->params->imageId(),
                'floatingIpPool'   => $this->params->floatingIpPool(),
                'useAutoconfig'    => $this->params->useAutoconfig(),
                'autoSecurityGroup'    => $this->params->autoSecurityGroup(),
                'isProxyGateway'   => $this->params->isProxyGateway(),
                'isPublic'         => $this->params->isPublic(),
                'isProtected'      => $this->params->isProtected(),
            ],
        ];
    }

    public function getJobBinaries(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-binaries',
            'params' => [
                'limit'   => $this->params->limit(),
                'marker'  => $this->params->marker(),
                'sortBy' => $this->params->sortBy()
            ],
        ];
    }

    public function getJobBinary(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-binaries/{id}',
            'params' => ['id' => $this->params->urlId('binary')],
        ];
    }

    public function postJobBinary(): array
    {
        return [
            'path'   => 'job-binaries',
            'method' => 'POST',
            'params' => [
                'url'         => $this->params->url(),
                'name'        => $this->params->name('job_binary'),
                'description' => $this->params->description(),
                'extra'       => $this->params->extra(),
            ],
        ];
    }

    public function deleteJobBinary(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'job-binaries/{id}',
            'params' => ['id' => $this->params->urlId('job_binary')],
        ];
    }

    public function putJobBinary(): array
    {
        return [
            'method' => 'PUT',
            'path'   => 'job-binaries/{id}',
            'params' => [
                'id'          => $this->params->urlId('job_binary'),
                'url'         => $this->notRequired($this->params->url()),
                'isPublic'    => $this->params->isPublic(),
                'name'        => $this->notRequired($this->params->name('job_binary')),
                'isProtected' => $this->params->isProtected(),
                'description' => $this->params->description(),
            ],
        ];
    }

    public function getJobBinaryData(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-binaries/{id}/data',
            'params' => ['id' => $this->params->urlId('job_binary')],
        ];
    }

    public function putJobBinaryInternal(): array
    {
        return [
            'method' => 'PUT',
            'path'   => 'job-binary-internals/{name}',
            'params' => [
                'name'        => $this->params->urlId('job_binary_internal'),
                'data'        => $this->params->data(),
                'contentType' => $this->params->contentType(),
            ],
        ];
    }

    public function getJobBinaryInternalData(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-binary-internals/{id}/data',
            'params' => [
                'id' => $this->params->urlId('job_binary_internal'),
            ],
        ];
    }

    public function getJobBinaryInternal(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-binary-internals/{id}',
            'params' => [
                'id' => $this->params->urlId('job_binary_internal'),
            ],
        ];
    }

    public function getJobBinaryInternals(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-binary-internals',
            'params' => [
                'limit'   => $this->params->limit(),
                'marker'  => $this->params->marker(),
                'sortBy' => $this->params->sortBy(),
            ],
        ];
    }

    public function deleteJobBinaryInternal(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'job-binary-internals/{id}',
            'params' => [
                'id' => $this->params->urlId('job_binary_internal'),
            ],
        ];
    }

    public function patchJobBinaryInternal(): array
    {
        return [
            'method' => 'PATCH',
            'path'   => 'job-binary-internals/{id}',
            'params' => [
                'id'          => $this->params->urlId('job_binary_internal'),
                'name'        => $this->params->name('job_binary_internal'),
                'isProtected' => $this->params->isProtected(),
                'isPublic'    => $this->params->isPublic(),
            ],
        ];
    }

    public function postJob(): array
    {
        return [
            'path'   => 'jobs',
            'method' => 'POST',
            'params' => [
                'description' => $this->params->description(),
                'mains'       => [
                    'type'        => params:: ARRAY_TYPE,
                    'description' => 'The list of the job object and their properties.',
                    'required'    => false,
                ],
                'libs'        => [
                    'type'        => params::ARRAY_TYPE,
                    'description' => 'The list of the job object properties.',
                    'required'    => false,
                ],
                'type'        => [
                    'type'        => params:: STRING_TYPE,
                    'description' => 'The type of the data source object.',
                    'required'    => true,
                ],
                'interface'   => [
                    'type'        => params:: ARRAY_TYPE,
                    'description'   => 'The interface of the job object.',
                    'required'              => false
                ],
                'name' => $this->isRequired($this->params->name('job')),
            ],
        ];
    }

    public function getJobs(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'jobs',
            'params' => [
                'limit'  => $this->params->limit(),
                'marker' => $this->params->marker(),
                'sortBy' => $this->params->sortkey(),
            ],
        ];
    }

    public function getJob(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'jobs/{id}',
            'params' => [
                'id' => $this->params->urlId('jobs'),
            ],
        ];
    }

    public function deleteJob(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'jobs/{id}',
            'params' => ['id' => $this->params->urlId('jobs')],
        ];
    }

    public function patchJob(): array
    {
        return [
            'method' => 'PATCH',
            'path'   => 'jobs/{id}',
            'params' => [
                'id'          => $this->params->urlId('jobs'),
                'name'        => $this->params->name('job'),
                'isProtected' => $this->params->isProtected(),
                'isPublic'    => $this->params->isPublic(),
                'description' => $this->params->description(),
            ],
        ];
    }

    public function executeJob(): array
    {
        return [
            'method' => 'POST',
            'path'   => 'jobs/{id}/execute',
            'params' => [
                'id'          => $this->params->urlId('jobs'),
                'isProtected' => $this->params->isProtected(),
                'isPublic'    => $this->params->isPublic(),
                'clusterId'   => [
                    'type'     => params::STRING_TYPE,
                    'required' => true,
                    'sentAs'   => 'cluster_id',
                ],
                'inputId'     => [
                    'type'     => params::STRING_TYPE,
                    'required' => false,
                    'sentAs'   => 'input_id',
                ],
                'outputId'    => [
                    'type'     => params::STRING_TYPE,
                    'required' => false,
                    'sentAs'   => 'output_id',
                ],
                'jobConfigs'  => [
                    'type'     => params::OBJECT_TYPE,
                    'required' => true,
                    'sentAs'   => 'job_configs',
                    'items'    => [
                        'properties'       => [
                            'configs'      => [
                                'type'     => params::OBJECT_TYPE,
                                'required' => true,
                            ],
                            'args'         => [
                                'type'     => params::ARRAY_TYPE,
                                'required' => false,
                            ],
                            'params'       => [
                                'type'     => params::OBJECT_TYPE,
                                'required' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getJobExecutions(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-executions',
            'params' => [
                'limit'  => $this->params->limit(),
                'marker' => $this->params->marker(),
                'sortBy' => $this->params->sortBy(),
            ],
        ];
    }

    public function getJobExecution(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-executions/{id}',
            'params' => [
                'id' => $this->params->urlId('jobs'),
            ],
        ];
    }

    public function deleteJobExecution(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'job-executions/{id}',
            'params' => ['id' => $this->params->urlId('job-executions')],
        ];
    }

    public function patchJobExecution(): array
    {
        return [
            'method' => 'PATCH',
            'path'   => 'job-executions/{id}',
            'params' => [
                'id'          => $this->params->urlId('job-executions'),
                'isPublic'    => $this->params->isPublic(),
                'isProtected' => $this->params->isProtected(),
            ],
        ];
    }

    public function refreshStatus(): array
    {
        $definition = $this->getJobExecution();
        $definition['path'] .= '/refresh-status';
        return $definition;
    }

    public function cancelJob(): array
    {
        $definition = $this->getJobExecution();
        $definition['path'] .= '/cancel';
        return $definition;
    }

    public function getPlugin(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'plugins/{name}',
            'params' => [
                'name' => $this->params->urlId('plugin'),
            ],
        ];
    }

    public function getPluginVersion(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'plugins/{name}/{versions}',
            'params' => [
                'name'     => $this->params->urlId('plugin'),
                'versions' => $this->params->version(),
            ],
        ];
    }

    public function patchPlugin(): array
    {
        return [
            'method' => 'PATCH',
            'path'   => 'plugins/{name}',
            'params' => [
                'name'          => $this->params->urlId('plugin'),
                'pluginLabels'  => $this->params->pluginLabels(),
                'versionLabels' => $this->params->versionlabels(),
            ],
        ];
    }

    public function getPlugins(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'plugins',
            'params' => [
            ],
        ];
    }

    public function getImages(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'images',
            'params' => [
            ],
        ];
    }

    public function getImage(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}',
            'params' => [
                'id' => $this->params->urlId('image'),
            ],
        ];
    }

    public function postImage(): array
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}',
            'params' => [
                'id'          => $this->params->urlId('image'),
                'username'    => $this->params->name('image'),
                'description' => $this->params->description(),
            ],
        ];
    }

    public function postImageTag(): array
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}/tag',
            'params' => [
                'id'   => $this->params->urlId('image'),
                'tags' => [
                    'type'        => params::ARRAY_TYPE,
                    'description' => 'tags array for image',
                    'required'    => false,
                ],
            ],
        ];
    }

    public function unPostImageTag(): array
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}/untag',
            'params' => [
                'id'   => $this->params->urlId('image'),
                'tags' => [
                    'type'        => params::ARRAY_TYPE,
                    'description' => 'tags array for image',
                    'required'    => false,
                ],
            ],
        ];
    }

    public function deleteImage(): array
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}',
            'params' => [
                'id' => $this->params->urlId('image'),
            ],
        ];
    }

    public function getJobTypes(String $path): array
    {
        return [
                'method' => 'GET',
                'path'   => 'job-types?'.$path,
                'params' => [
                    'plugin'  => $this->params->plugin_filter(),
                    'version' => $this->params->version_filter(),
                    'type'    => $this->params->type(),
                    'hints'   => $this->params->hints(),
                ],
        ];
    }

    public function getNodeGroups(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'clusters',
            'params' => [
            ],
        ];
    }

    public function getNodeGroup(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'clusters',
            'params' => [
            ],
        ];
    }

    public function getJobConfigs(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-executions',
            'params' => [
            ],
        ];
    }

    public function getJobConfig(): array
    {
        return [
            'method' => 'GET',
            'path'   => 'job-executions/{id}',
            'params' => [
                'id' => $this->params->urlId('jobConfigs'),
            ],
        ];
    }
}
