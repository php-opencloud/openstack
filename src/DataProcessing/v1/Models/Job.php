<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

class Job extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    public $description;
    public $tenantId;
    public $createdAt;
    public $mains;
    public $updatedAt;
    public $libs;
    public $isProtected;
    public $interface;
    public $type;
    public $isPublic;
    public $id;
    public $name;

    protected $resourceKey = 'job';
    protected $resourcesKey = 'jobs';

    protected $aliases = [
        'created_at'   => 'createdAt',
        'updated_at'   => 'updatedAt',
        'is_protected' => 'isProtected',
        'tenant_id'    => 'tenantId',
        'is_public'    => 'isPublic',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getJob(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postJob(), $userOptions);

        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteJob(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->patchJob(), $this->getAttrs(['id', 'name', 'isPublic', 'description', 'isProtected']));
        $this->populateFromResponse($response);
    }

    public function executeJob(array $options)
    {
        $response = $this->execute($this->api->executeJob(), $options);

        return $this->model(JobExecution::class)->populateFromResponse($response);
    }

    public function getJobTypes(array $options = []): array
    {
        $path = '';
        if ($options !== []) {
            $path .= '?';
        }
        if (array_key_exists('plugin', $options)) {
            $path .= '&plugin={plugin}';
        }
        if (array_key_exists('version', $options)) {
            $path .= '&version={version}';
        }
        if (array_key_exists('hints', $options)) {
            $path .= '&hints={hints}';
        }
        $response = $this->execute($this->api->getJobTypes($path), $options);

        return Utils::jsonDecode($response);
    }
}
