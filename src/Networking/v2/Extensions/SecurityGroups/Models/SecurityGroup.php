<?php declare(strict_types=1);

namespace OpenStack\Networking\v2\Extensions\SecurityGroups\Models;

use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Resource\Updateable;

/**
 * Represents a SecurityGroup resource in the Network v2 service
 *
 * @property \OpenStack\Networking\v2\Extensions\SecurityGroups\Api $api
 */
class SecurityGroup extends OperatorResource implements Creatable, Listable, Deletable, Retrievable, Updateable
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
        'rules'                => 'securityGroupRules',
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

    public function update()
    {
        $response = $this->executeWithState($this->api->putSecurityGroups());
        $this->populateFromResponse($response);
    }
}
