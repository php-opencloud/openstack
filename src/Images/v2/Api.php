<?php

namespace OpenStack\Images\v2;

use OpenStack\Common\Api\AbstractApi;

class Api extends AbstractApi
{
    private $basePath;

    public function __construct()
    {
        $this->params   = new Params;
        $this->basePath = 'v2/';
    }

    public function postImages()
    {
        return [
            'method' => 'POST',
            'path'   => $this->basePath . 'images',
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
            'path'   => $this->basePath . 'images',
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
            'path'   => $this->basePath . 'images/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }

    public function patchImage()
    {
        return [
            'method' => 'PATCH',
            'path'   => $this->basePath . 'images/{id}',
            'params' => [
                'id'          => $this->params->idPath(),
                'patchDoc'    => $this->params->patchDoc(),
                'contentType' => $this->params->contentType(),
            ],
        ];
    }

    public function deleteImage()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->basePath . 'images/{id}',
            'params' => ['id' => $this->params->idPath()],
        ];
    }

    public function reactivateImage()
    {
        return [
            'method' => 'POST',
            'path'   => $this->basePath . 'images/{id}/actions/reactivate',
            'params' => ['id' => $this->params->idPath()],
        ];
    }

    public function deactivateImage()
    {
        return [
            'method' => 'POST',
            'path'   => $this->basePath . 'images/{id}/actions/deactivate',
            'params' => ['id' => $this->params->idPath()],
        ];
    }

    public function postImageData()
    {
        return [
            'method' => 'PUT',
            'path'   => $this->basePath . 'images/{id}/file',
            'params' => [
                'id'          => $this->params->idPath(),
                'data'        => $this->params->data(),
                'contentType' => $this->params->contentType(),
            ]
        ];
    }

    public function getImageData()
    {
        return [
            'method' => 'GET',
            'path'   => $this->basePath . 'images/{id}/file',
            'params' => ['id' => $this->params->idPath()]
        ];
    }

    public function getImageSchema()
    {
        return [
            'method' => 'GET',
            'path'   => $this->basePath . 'schemas/image',
            'params' => [],
        ];
    }

    public function postImageMembers()
    {
        return [
            'method' => 'POST',
            'path'   => $this->basePath . 'images/{imageId}/members',
            'params' => [
                'imageId' => $this->params->idPath(),
                'id'      => $this->params->memberId(),
            ],
        ];
    }

    public function getImageMembers()
    {
        return [
            'method' => 'GET',
            'path'   => $this->basePath . 'images/{imageId}/members',
            'params' => ['imageId' => $this->params->idPath()],
        ];
    }

    public function getImageMember()
    {
        return [
            'method' => 'GET',
            'path'   => $this->basePath . 'images/{imageId}/members/{id}',
            'params' => [
                'imageId' => $this->params->idPath(),
                'id'      => $this->params->idPath(),
            ],
        ];
    }

    public function deleteImageMember()
    {
        return [
            'method' => 'DELETE',
            'path'   => $this->basePath . 'images/{imageId}/members/{id}',
            'params' => [
                'imageId' => $this->params->idPath(),
                'id'      => $this->params->idPath(),
            ],
        ];
    }

    public function putImageMember()
    {
        return [
            'method' => 'PUT',
            'path'   => $this->basePath . 'images/{imageId}/members/{id}',
            'params' => [
                'imageId' => $this->params->idPath(),
                'id'      => $this->params->idPath(),
                'status'  => $this->params->status(),
            ],
        ];
    }
}