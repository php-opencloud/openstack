<?php

namespace OpenStack\DataProcessing\v1;

use OpenStack\Common\Service\AbstractService;
use OpenStack\Common\Transport\Utils;
use OpenStack\DataProcessing\v1\Models\Cluster;
use OpenStack\DataProcessing\v1\Models\ClusterTemplate;
use OpenStack\DataProcessing\v1\Models\DataSource;
use OpenStack\DataProcessing\v1\Models\Image;
use OpenStack\DataProcessing\v1\Models\Job;
use OpenStack\DataProcessing\v1\Models\JobBinary;
use OpenStack\DataProcessing\v1\Models\JobBinaryInternal;
use OpenStack\DataProcessing\v1\Models\JobConfig;
use OpenStack\DataProcessing\v1\Models\JobExecution;
use OpenStack\DataProcessing\v1\Models\NodeGroup;
use OpenStack\DataProcessing\v1\Models\NodeGroupTemplate;
use OpenStack\DataProcessing\v1\Models\Plugin;
use Psr\Http\Message\StreamInterface;

class Service extends AbstractService
{
    public function listClusters(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(Cluster::class)->enumerate($this->api->getClusters(), $options, $mapFn);
    }

    public function getCluster(array $options = []): Cluster
    {
        $cluster = $this->model(Cluster::class);
        $cluster->populateFromArray($options);

        return $cluster;
    }

    public function createCluster(array $options = []): Cluster
    {
        return $this->model(Cluster::class)->create($options);
    }

    public function createMultipleClusters(array $options = [])
    {
        if (!array_key_exists("count", $options)) {
            throw new \RuntimeException("Require 'count'");
        }

        $response = $this->execute($this->api->postClusters(), $options);
        # For multiple clusters, the current API returns only cluster IDs.
        $ids = Utils::flattenJson(Utils::jsonDecode($response), 'clusters');
        if ($response->getStatusCode() === 204 || empty($ids)) {
            return;
        }
        foreach ($ids as $id) {
            $cluster = $this->model(Cluster::class);
            $cluster->id = $id;
            yield $cluster;
        }
    }

    public function scaleCluster(array $options = []): Cluster
    {
        return $this->model(Cluster::class)->scale($options);
    }

    public function createDataSource(array $options = []): Datasource
    {
        return $this->model(DataSource::class)->create($options);
    }

    public function getDataSource(array $options = []): Datasource
    {
        $source = $this->model(DataSource::class);
        $source->populateFromArray($options);

        return $source;
    }

    public function listDataSources(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(DataSource::class)->enumerate($this->api->getDataSources(), $options, $mapFn);
    }

    public function createClusterTemplate(array $options = []): ClusterTemplate
    {
        return $this->model(ClusterTemplate::class)->create($options);
    }

    public function getClusterTemplate(array $options = []): ClusterTemplate
    {
        $clusterTemplate = $this->model(ClusterTemplate::class);
        $clusterTemplate->populateFromArray($options);

        return $clusterTemplate;
    }

    public function listClusterTemplates(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(ClusterTemplate::class)->enumerate($this->api->getClusterTemplates(), $options, $mapFn);
    }

    public function getNodeGroupTemplate(array $options = []): NodeGroupTemplate
    {
        $nodeGroupTemplate = $this->model(NodeGroupTemplate::class);
        $nodeGroupTemplate->populateFromArray($options);

        return $nodeGroupTemplate;
    }

    public function listNodeGroupTemplates(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(NodeGroupTemplate::class)->enumerate($this->api->getNodeGroupTemplates(), $options, $mapFn);
    }

    public function createNodeGroupTemplate(array $options = []): NodeGroupTemplate
    {
        return $this->model(NodeGroupTemplate::class)->create($options);
    }

    public function listJobBinaries(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(JobBinary::class)->enumerate($this->api->getJobBinaries(), $options, $mapFn);
    }

    public function getJobBinary(array $options = []): JobBinary
    {
        $binary = $this->model(JobBinary::class);
        $binary->populateFromArray($options);

        return $binary;
    }

    public function createJobBinary(array $options = []): JobBinary
    {
        return $this->model(JobBinary::class)->create($options);
    }

    public function getJobBinaryInternal(array $options = []): JobBinaryInternal
    {
        $jobBinaryInternal = $this->model(JobBinaryInternal::class);
        $jobBinaryInternal->populateFromArray($options);

        return $jobBinaryInternal;
    }

    public function listJobBinaryInternals(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(JobBinaryInternal::class)->enumerate($this->api->getJobBinaryInternals(), $options, $mapFn);
    }

    public function createJobBinaryInternal(StreamInterface $stream): JobBinaryInternal
    {
        $options = [
            'name' => $stream->getMetadata('uri'),
            'data' => $stream,
        ];

        return $this->model(JobBinaryInternal::class)->create($options);
    }

    public function createJob(array $options = []): Job
    {
        return $this->model(Job::class)->create($options);
    }

    public function getJob(array $options = []): Job
    {
        $Job = $this->model(Job::class);
        $Job->populateFromArray($options);

        return $Job;
    }

    public function listJobs(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(Job::class)->enumerate($this->api->getJobs(), $options, $mapFn);
    }

    public function getJobExecution(array $options = []): JobExecution
    {
        $JobExecution = $this->model(JobExecution::class);
        $JobExecution->populateFromArray($options);

        return $JobExecution;
    }

    public function listJobExecutions(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(JobExecution::class)->enumerate($this->api->getJobExecutions(), $options, $mapFn);
    }

    public function runJob(array $options = []): JobExecution
    {
        return $this->getJob($options)->executeJob($options);
    }

    public function getPlugin(array $options = []): Plugin
    {
        $plugin = $this->model(Plugin::class);
        $plugin->populateFromArray($options);

        return $plugin;
    }

    public function listPlugins(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(Plugin::class)->enumerate($this->api->getPlugins(), $options, $mapFn);
    }

    public function getImage(array $options = []): Image
    {
        $image = $this->model(Image::class);
        $image->populateFromArray($options);

        return $image;
    }

    public function listImages(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(Image::class)->enumerate($this->api->getImages(), $options, $mapFn);
    }

    public function listJobTypes(array $options = [], callable $mapFn = null): array
    {
        return $this->model(Job::class)->getJobTypes($options);
    }

    public function listNodeGroups(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(NodeGroup::class)->enumerate($this->api->getNodeGroups(), $options, $mapFn);
    }

    public function getNodeGroup(array $options = [], callable $mapFn = null): array
    {
        $nodeGroups = $this->model(NodeGroup::class)->enumerate($this->api->getNodeGroup(), $options, $mapFn);
        foreach ($nodeGroups as $nodeGroup) {
            $nodegroups = $nodeGroup->nodeGroups;
            foreach ($nodegroups as $nodegroup) {
                if ($nodegroup['id'] === $options['id']) {
                    return $nodegroup;
                }
            }
        }

        return [];
    }

    public function listJobConfigs(array $options = [], callable $mapFn = null): \Generator
    {
        return $this->model(JobConfig::class)->enumerate($this->api->getJobConfigs(), $options, $mapFn);
    }

    public function getJobConfig(array $options = []): JobConfig
    {
        $jobConfig = $this->model(JobConfig::class);
        $jobConfig->populateFromArray($options);

        return $jobConfig;
    }
}
