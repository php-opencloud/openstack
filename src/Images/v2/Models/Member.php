<?php declare (strict_types=1);

namespace OpenStack\Images\v2\Models;

use OpenCloud\Common\Resource\AbstractResource;
use OpenCloud\Common\Resource\Creatable;
use OpenCloud\Common\Resource\Deletable;
use OpenCloud\Common\Resource\Listable;
use OpenCloud\Common\Resource\Retrievable;

/**
 * @property \OpenStack\Images\v2\Api $api
 */
class Member extends AbstractResource implements Creatable, Listable, Retrievable, Deletable
{
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_PENDING  = 'pending';
    const STATUS_REJECTED = 'rejected';

    /** @var string */
    public $imageId;

    /** @var string */
    public $id;

    /** @var \DateTimeImmutable */
    public $createdAt;

    /** @var \DateTimeImmutable */
    public $updatedAt;

    /** @var string */
    public $schemaUri;

    /** @var string */
    public $status;

    protected $aliases = [
        'created_at' => 'createdAt',
        'updated_at' => 'updatedAt',
        'member_id'  => 'id',
        'image_id'   => 'imageId',
    ];

    public function create(array $userOptions): Creatable
    {
        $response = $this->executeWithState($this->api->postImageMembers());
        return $this->populateFromResponse($response);
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getImageMember());
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->executeWithState($this->api->deleteImageMember());
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $this->executeWithState($this->api->putImageMember());
    }
}
