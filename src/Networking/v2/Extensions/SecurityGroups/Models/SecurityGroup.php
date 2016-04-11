<?php declare (strict_types=1);

namespace OpenStack\Networking\v2\Extensions\SecurityGroups\Models;

use OpenCloud\Common\Resource\OperatorResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Retrievable;

/**
 * Represents a SecurityGroup resource in the Network v2 service
 *
 * @property \OpenStack\Networking\v2\Extensions\SecurityGroups\Api $api
 */
class SecurityGroup extends OperatorResource implements Creatable, Listable, Deletable, Retrievable
{
    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var []SecurityGroupRule
     */
    public $securityGroupRules;

    /**
     * @var string
     */
    public $tenantId;

    protected $aliases = [
        'security_group_rules' => 'securityGroupRules',
        'tenant_id'            => 'tenantId',
    ];

    protected $resourceKey  = 'security_group';
    protected $resourcesKey = 'security_groups';

    /**
     * {@inheritDoc}
     */
    public function create(array $userOptions): Creatable
    {
        $response = $this->execute($this->api->postSecurityGroups(), $userOptions);
        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->executeWithState($this->api->deleteSecurityGroup());
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getSecurityGroup());
        $this->populateFromResponse($response);
    }
}
