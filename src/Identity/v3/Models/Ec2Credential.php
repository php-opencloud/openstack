<?php

declare( strict_types=1 );

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class Ec2Credential extends OperatorResource implements Listable
{
    /** @var string */
    public $access;

    /** @var string */
    public $secret;

    /** @var string */
    public $userId;

    /** @var string */
    public $tenantId;

    protected $resourceKey  = 'credential';
    protected $resourcesKey = 'credentials';
    protected $aliases      = [
        'user_id'   => 'userId',
        'tenant_id' => 'tenantId'
    ];

    /**
     * {@inheritdoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postEc2Credential}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postEc2Credential(), $data);

        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getEc2Credential());
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $userOptions = [])
    {
        $this->execute($this->api->deleteEc2Credential(), $this->getAttrs(['access', 'userId']));
    }
}
