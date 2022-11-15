<?php

declare( strict_types=1 );

namespace OpenStack\Identity\v3\Models;

use OpenStack\Common\Resource\Alias;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;

/**
 * @property \OpenStack\Identity\v3\Api $api
 */
class ApplicationCredential extends OperatorResource implements Listable
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $userId;

    /** @var string */
    public $projectId;

    /** @var string */
    public $expiresAt;

    /** @var bool */
    public $unrestricted;

    /** @var string */
    public $secret;

    protected $resourceKey  = 'application_credential';
    protected $resourcesKey = 'application_credentials';
    protected $aliases      = [
        'user_id'    => 'userId',
        'project_id' => 'projectId'
    ];

    /**
     * {@inheritdoc}
     *
     * @param array $data {@see \OpenStack\Identity\v3\Api::postApplicationCredential}
     */
    public function create(array $data)
    {
        $response = $this->execute($this->api->postApplicationCredential(), $data);

        return $this->populateFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getApplicationCredential());
        $this->populateFromResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $this->execute($this->api->deleteApplicationCredential(), $this->getAttrs(['id', 'userId']));
    }
}
