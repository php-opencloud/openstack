<?php

namespace OpenStack\DataProcessing\v1\Models;

use OpenStack\Common\Resource\Creatable;
use OpenStack\Common\Resource\Deletable;
use OpenStack\Common\Resource\Listable;
use OpenStack\Common\Resource\OperatorResource;
use OpenStack\Common\Resource\Retrievable;

class Image extends OperatorResource implements Listable, Retrievable, Deletable
{
    public $status;
    public $username;
    public $updated;
    public $OSEXTIMGSIZEsize;
    public $created;
    public $tags;
    public $minDisk;
    public $name;
    public $progress;
    public $minRam;
    public $metadata;
    public $id;
    public $description;

    protected $resourceKey = 'image';
    protected $resourcesKey = 'images';

    protected $aliases = [
        'OS-EXT-IMG-SIZE:size' => 'OSEXTIMGSIZEsize',
    ];

    public function retrieve()
    {
        $response = $this->execute($this->api->getImage(), $this->getAttrs(['id']));
        $this->populateFromResponse($response);
    }

    public function delete()
    {
        $this->execute($this->api->deleteImage(), $this->getAttrs(['id']));
    }

    public function update()
    {
        $response = $this->execute($this->api->patchImage(), $this->getAttrs(['id', 'description', 'name', 'isPublic', 'isProtected']));
        $this->populateFromResponse($response);
    }

    public function register(array $userOptions)
    {
        $response = $this->execute($this->api->postImage(), array_merge($this->getAttrs(['id']), $userOptions));

        return $this->populateFromResponse($response);
    }

    public function unregister()
    {
        $this->execute($this->api->deleteImage(), $this->getAttrs(['id']));
    }

    public function addTags(array $userOptions)
    {
        $options = array_merge($this->getAttrs(['id']), $userOptions);
        $response = $this->execute($this->api->postImageTag(), $options);

        return $this->populateFromResponse($response);
    }

    public function removeTags(array $userOptions)
    {
        $options = array_merge($this->getAttrs(['id']), $userOptions);
        $response = $this->execute($this->api->unPostImageTag(), $options);

        return $this->populateFromResponse($response);
    }
}
