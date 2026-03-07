<?php

declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

/**
 * Represents a Compute v2 Server Group.
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class ServerGroup extends OperatorResource implements Listable, Retrievable, Creatable, Deletable
{
    /** @var string */
    public $id;

    /** @var array */
    public $members = [];

    /** @var array */
    public $metadata = [];

    /** @var string */
    public $name;

    /** @var string */
    public $policy;

    /** @var array */
    public $policies = [];

    /** @var string */
    public $projectId;

    /** @var array */
    public $rules = [];

    /** @var string */
    public $userId;

    protected $aliases = [
        'project_id' => 'projectId',
        'user_id'    => 'userId',
    ];

    protected $resourceKey  = 'server_group';
    protected $resourcesKey = 'server_groups';

    public function retrieve()
    {
        $response = $this->execute($this->api->getServerGroup(), ['id' => (string) $this->id]);
        $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        if (isset($userOptions['policy'], $userOptions['policies'])) {
            throw new \RuntimeException('Provide either "policy" or "policies", not both.');
        }

        if (isset($userOptions['rules']) && !isset($userOptions['policy'])) {
            throw new \RuntimeException('"rules" requires "policy".');
        }

        if (!isset($userOptions['policy']) && !isset($userOptions['policies'])) {
            throw new \RuntimeException('"policy" or "policies" must be set.');
        }

        $definition = isset($userOptions['policy'])
            ? $this->api->postServerGroupWithPolicy()
            : $this->api->postServerGroup();

        $response = $this->execute($definition, $userOptions);

        return $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteServerGroup(), ['id' => (string) $this->id]);
    }

    public function populateFromArray(array $array): self
    {
        $array = Utils::flattenJson($array, $this->resourceKey);

        if (isset($array['policy']) && !isset($array['policies'])) {
            $array['policies'] = [$array['policy']];
        }

        if (isset($array['policies']) && is_array($array['policies']) && !isset($array['policy']) && count($array['policies']) > 0) {
            $array['policy'] = reset($array['policies']);
        }

        $array += [
            'members'  => [],
            'metadata' => [],
            'policies' => [],
            'rules'    => [],
        ];

        return parent::populateFromArray($array);
    }
}
