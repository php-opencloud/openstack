<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;

class JobExecution extends OperatorResource implements Listable, Retrievable, Deletable
{
    public $jobConfigs;
    public $isProtected;
    public $inputId;
    public $jobId;
    public $clusterId;
    public $createdAt;
    public $endTime;
    public $outputId;
    public $isPublic;
    public $updatedAt;
    public $returnCode;
    public $dataSourceUrls;
    public $tenantId;
    public $startTime;
    public $id;
    public $oozieJobId;
    public $info;
    public $createdTime;
    public $status;
    public $group;
    public $externalId;
    public $acl;
    public $run;
    public $appName;
    public $parentId;
    public $conf;
    public $appPath;
    public $toString;
    public $lastModTime;
    public $consoleUrl;

    protected $resourceKey = 'job_execution';
    protected $resourcesKey = 'job_executions';

    protected $aliases = [
                'job_configs'      => 'jobConfigs',
                'is_protected'     => 'isProtected',
                'input_id'         => 'inputId',
                'job_id'           => 'jobId',
                'cluster_id'       => 'clusterId',
                'created_at'       => 'createdAt',
                'end_time'         => 'endTime',
                'output_id'        => 'outputId',
                'is_public'        => 'isPublic',
                'updated_at'       => 'updatedAt',
                'reutrn_code'      => 'returnCode',
                'data_source_urls' => 'dataSourceUrls',
                'tenant_id'        => 'tenantId',
                'start_time'       => 'startTime',
                'oozie_job_id'     => 'oozieJobId',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getJobExecution(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteJobExecution(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->patchJobExecution(), $this->getAttrs(['id', 'isPublic', 'isProtected']));
        $this->populateFromResponse($response);
    }

    public function cancel()
    {
        $response = $this->execute($this->api->cancelJob(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function refreshStatus()
    {
        $response = $this->execute($this->api->refreshStatus(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }
}
