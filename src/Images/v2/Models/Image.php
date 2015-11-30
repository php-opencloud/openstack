<?php

namespace OpenStack\Images\v2\Models;

use function GuzzleHttp\Psr7\uri_for;

use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\Retrievable;
use OpenStack\Common\Transport\Utils;

/**
 * @property \OpenStack\Images\v2\Api $api
 */
class Image extends AbstractResource implements Creatable, Listable, Retrievable
{
    /** @var string */
    public $status;

    /** @var string */
    public $name;

    /** @var array */
    public $tags;

    /** @var string */
    public $containerFormat;

    /** @var \DateTimeImmutable */
    public $createdAt;

    /** @var string */
    public $diskFormat;

    /** @var \DateTimeImmutable */
    public $updatedAt;

    /** @var string */
    public $visibility;

    /** @var int */
    public $minDisk;

    /** @var bool */
    public $protected;

    /** @var string */
    public $id;

    /** @var \GuzzleHttp\Psr7\Uri */
    public $fileUri;

    /** @var string */
    public $checksum;

    /** @var string */
    public $ownerId;

    /** @var int */
    public $size;

    /** @var int */
    public $minRam;

    /** @var \GuzzleHttp\Psr7\Uri */
    public $schemaUri;

    /** @var int */
    public $virtualSize;

    protected $aliases = [
        'container_format' => 'containerFormat',
        'created_at'       => 'createdAt',
        'disk_format'      => 'diskFormat',
        'updated_at'       => 'updatedAt',
        'min_disk'         => 'minDisk',
        'owner'            => 'ownerId',
        'min_ram'          => 'minRam',
        'virtual_size'     => 'virtualSize',
    ];

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $baseUri = $this->getHttpBaseUrl();

        if (isset($data['file'])) {
            $this->fileUri = Utils::appendPath($baseUri, $data['file']);
        }

        if (isset($data['schema'])) {
            $this->schemaUri = Utils::appendPath($baseUri, $data['schema']);
        }
    }

    public function create(array $data)
    {
        $response = $this->execute($this->api->postImages(), $data);
        return $this->populateFromResponse($response);
    }

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getImage());
        return $this->populateFromResponse($response);
    }

    public function update(array $data)
    {
        // validate against schema

        // create json patch doc based on changeset
    }
}