<?php

declare(strict_types=1);

namespace OpenStack\Networking\v2\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\HasWaiterTrait;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Networking\v2\Api;

/**
 * @property Api $api
 */
class RbacPolicy extends OperatorResource implements Creatable, Deletable, Listable, Retrievable
{
    use HasWaiterTrait;

    /**
     * The ID of the tenant to which the RBAC policy will be enforced
     *
     * @var string
     */
    public $targetTenant;

    /**
     * The ID of the project that owns the resource
     *
     * @var string
     */
    public $tenantId;

    /**
     * The type of the object that the RBAC policy affects. Types include qos-policy, network, security-group,
     * address-scope, subnetpool or address-group
     *
     * @var string
     */
    public $objectType;

    /**
     * The ID of the object_type resource. An object_type of network returns a network ID, an object_type of qos-policy
     * returns a QoS policy ID, an object_type of security-group returns a security group ID, an object_type of
     * address-scope returns a address scope ID, an object_type of subnetpool returns a subnetpool ID and an
     * object_type of address-group returns an address group ID
     *
     * @var string
     */
    public $objectId;

    /**
     * Action for the RBAC policy which is access_as_external or access_as_shared
     *
     * @var string
     */
    public $action;

    /**
     * The ID of the project.
     *
     * @var string
     */
    public $projectId;

    /**
     * The ID of the RBAC policy
     *
     * @var string
     */
    public $id;

    protected $aliases = [
        'target_tenant' => 'targetTenant',
        'tenant_id'     => 'tenantId',
        'object_type'   => 'objectType',
        'object_id'     => 'objectId',
        'project_id'    => 'projectId',
    ];

    protected $resourceKey  = 'rbac_policy';
    protected $resourcesKey = 'rbac_policies';

    /**
     * {@inheritDoc}
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postRbacPolicy(), $userOptions);

        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $reponse = $this->execute($this->api->getRbacPolicy(), ['id' => (string) $this->id]);
        $this->populateFromResponse($reponse);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(array $userOptions = [])
    {
        $this->executeWithState($this->api->deleteRbacPolicy());
    }
}