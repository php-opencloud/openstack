<?php

namespace OpenStack\Images\v2;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params;
    }

    public function postImages()
    {
        return [
            'method' => 'POST',
            'path'   => 'images',
            'params' => [
                'name'            => $this->params->imageName(),
                'visibility'      => $this->params->visibility(),
                'tags'            => $this->params->tags(),
                'containerFormat' => $this->params->containerFormat(),
                'diskFormat'      => $this->params->diskFormat(),
                'minDisk'         => $this->params->minDisk(),
                'minRam'          => $this->params->minRam(),
                'protected'       => $this->params->protectedParam(),
            ],
        ];
    }

    public function getImages()
    {
        return [
            'method' => 'GET',
            'path'   => 'images',
            'params' => [
                'limit'        => $this->params->limit(),
                'marker'       => $this->params->marker(),
                'sortKey'      => $this->params->sortKey(),
                'sortDir'      => $this->params->sortDir(),
                'name'         => $this->params->queryName(),
                'visibility'   => $this->params->queryVisibility(),
                'memberStatus' => $this->params->queryMemberStatus(),
                'owner'        => $this->params->queryOwner(),
                'status'       => $this->params->queryStatus(),
                'sizeMin'      => $this->params->querySizeMin(),
                'sizeMax'      => $this->params->querySizeMax(),
                'tag'          => $this->params->queryTag(),
            ],
        ];
    }

    public function getImage()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }
}