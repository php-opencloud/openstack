<?php

declare(strict_types=1);

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;

/**
 * Represents a Compute v2 ExtraSpecs.
 *
 * @property \OpenStack\Compute\v2\Api $api
 */
class ExtraSpecs extends OperatorResource implements Retrievable, Creatable
{
    protected $resourceKey  = 'extra_specs';

    /** @var int */
    public $diskIoLimit;

    /** @var int */
    public $diskWriteBytesSec;

    /** @var int */
    public $diskReadBytesSec;

    /** @var string */
    public $flavorId;

    protected $aliases = [
        'disk_io_limit'  => 'diskIoLimit',
        'disk_write_bytes_sec'  => 'diskWriteBytesSec',
        'disk_read_bytes_sec'  => 'diskReadBytesSec',
    ];

    public function retrieve()
    {
        $response = $this->executeWithState($this->api->getExtraSpecs());
        return $this->populateFromResponse($response);
    }

    public function create(array $userOptions): Creatable
    {
        $this->populateFromArray($userOptions);
        $response = $this->executeWithState($this->api->postExtraSpecs());
        return $this->populateFromResponse($response);
    }

    public function deleteExtraSpec(string $extraSpec)
    {
        $extraSpecKey = array_search($extraSpec, $this->aliases, true);
        $this->execute(
            $this->api->deleteExtraSpec(),
            [
                'flavorId' => $this->flavorId,
                'extraSpecKey' => $extraSpecKey ?: $extraSpec,
            ]
        );
    }
}