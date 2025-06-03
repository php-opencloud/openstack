<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use Psr\Http\Message\StreamInterface;

class JobBinaryInternal extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    public $name;
    public $tenantId;
    public $createdAt;
    public $updatedAt;
    public $isProtected;
    public $isPublic;
    public $datasize;
    public $id;

    protected $resourceKey = 'job_binary_internal';
    protected $resourcesKey = 'binaries';

    protected $aliases = [
        'tenant_id'    => 'tenantId',
        'created_at'   => 'createdAt',
        'updated_at'   => 'updatedAt',
        'is_protected' => 'isProtected',
        'is_public'    => 'isPublic',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getJobBinaryInternal(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        $options = array_merge($userOptions, [
      'contentType' => 'application/octet-stream',
    ]);
        $response = $this->execute($this->api->putJobBinaryInternal(), $options);

        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteJobBinaryInternal(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->patchJobBinaryInternal(), $this->getAttrs(['id', 'name', 'isProtected', 'isPublic']));
        $this->populateFromResponse($response);
    }

    public function downloadData(): StreamInterface
    {
        $response = $this->executeWithState($this->api->getJobBinaryInternalData());
        return $response->getBody();
    }
}
