<?php

namespace OpenStack\Compute\v2;

use OpenStack\Common\Api\AbstractApi;

/**
 * A representation of the Compute (Nova) v2 REST API.
 *
 * @internal
 * @package OpenStack\Compute\v2
 */
class Api extends AbstractApi
{
    public function __construct()
    {
        $this->params = new Params();
    }

    public function getFlavors()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors',
            'params' => [
                'limit'   => $this->params->limit(),
                'marker'  => $this->params->marker(),
                'minDisk' => $this->params->minDisk(),
                'minRam'  => $this->params->minRam(),
            ],
        ];
    }

    public function getFlavorsDetail()
    {
        $op = $this->getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public function getFlavor()
    {
        return [
            'method' => 'GET',
            'path'   => 'flavors/{id}',
            'params' => ['id' => $this->params->urlId('flavor')]
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
                'name'         => $this->params->flavorName(),
                'changesSince' => $this->params->filterChangesSince('image'),
                'server'       => $this->params->flavorServer(),
                'status'       => $this->params->filterStatus('image'),
                'type'         => $this->params->flavorType(),
            ],
        ];
    }

    public function getImagesDetail()
    {
        $op = $this->getAll();
        $op['path'] += '/detail';
        return $op;
    }

    public function getImage()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}',
            'params' => ['id' => $this->params->urlId('image')]
        ];
    }

    public function deleteImage()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}',
            'params' => ['id' => $this->params->urlId('image')]
        ];
    }

    public function getImageMetadata()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata',
            'params' => ['id' => $this->params->urlId('image')]
        ];
    }

    public function putImageMetadata()
    {
        return [
            'method' => 'PUT',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id'       => $this->params->urlId('image'),
                'metadata' => $this->params->metadata(),
            ]
        ];
    }

    public function postImageMetadata()
    {
        return [
            'method' => 'POST',
            'path'   => 'images/{id}/metadata',
            'params' => [
                'id'       => $this->params->urlId('image'),
                'metadata' => $this->params->metadata()
            ]
        ];
    }

    public function getImageMetadataKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id'  => $this->params->urlId('image'),
                'key' => $this->params->key(),
            ]
        ];
    }

    public function deleteImageMetadataKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'images/{id}/metadata/{key}',
            'params' => [
                'id'  => $this->params->urlId('image'),
                'key' => $this->params->key(),
            ]
        ];
    }

    public function postServer()
    {
        return [
            'path'    => 'servers',
            'method'  => 'POST',
            'jsonKey' => 'server',
            'params'  => [
                'imageId'            => $this->params->imageId(),
                'flavorId'           => $this->params->flavorId(),
                'personality'        => $this->params->personality(),
                'metadata'           => $this->notRequired($this->params->metadata()),
                'name'               => $this->isRequired($this->params->name('server')),
                'securityGroups'     => $this->params->securityGroups(),
                'userData'           => $this->params->userData(),
                'availabilityZone'   => $this->params->availabilityZone(),
                'networks'           => $this->params->networks(),
                'blockDeviceMapping' => $this->params->blockDeviceMapping(),
            ]
        ];
    }

    public function getServers()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers',
            'params' => [
                'limit'        => $this->params->limit(),
                'marker'       => $this->params->marker(),
                'changesSince' => $this->params->filterChangesSince('server'),
                'imageId'      => $this->params->filterImage(),
                'flavorId'     => $this->params->filterFlavor(),
                'name'         => $this->params->filterName(),
                'status'       => $this->params->filterStatus('server'),
                'host'         => $this->params->filterHost(),
            ],
        ];
    }

    public function getServersDetail()
    {
        $definition = $this->getServers();
        $definition['path'] += '/detail';
        return $definition;
    }

    public function getServer()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}',
            'params' => ['id' => $this->params->urlId('server')]
        ];
    }

    public function putServer()
    {
        return [
            'method'  => 'PUT',
            'path'    => 'servers/{id}',
            'jsonKey' => 'server',
            'params'  => [
                'id'   => $this->params->urlId('server'),
                'ipv4' => $this->params->ipv4(),
                'ipv6' => $this->params->ipv6(),
                'name' => $this->params->name('server'),
            ],
        ];
    }

    public function deleteServer()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}',
            'params' => ['id' => $this->params->urlId('server')],
        ];
    }

    public function changeServerPassword()
    {
        return [
            'method'  => 'POST',
            'path'    => 'servers/{id}/action',
            'jsonKey' => 'changePassword',
            'params'  => [
                'id'       => $this->params->urlId('server'),
                'password' => $this->params->password(),
            ],
        ];
    }

    public function rebootServer()
    {
        return [
            'method'  => 'POST',
            'path'    => 'servers/{id}/action',
            'jsonKey' => 'reboot',
            'params'  => [
                'id'   => $this->params->urlId('server'),
                'type' => $this->params->rebootType(),
            ],
        ];
    }

    public function rebuildServer()
    {
        return [
            'method'  => 'POST',
            'path'    => 'servers/{id}/action',
            'jsonKey' => 'rebuild',
            'params'  => [
                'id'          => $this->params->urlId('server'),
                'ipv4'        => $this->params->ipv4(),
                'ipv6'        => $this->params->ipv6(),
                'imageId'     => $this->params->imageId(),
                'personality' => $this->params->personality(),
                'name'        => $this->params->name('server'),
                'metadata'    => $this->notRequired($this->params->metadata()),
                'adminPass'   => $this->params->password(),
            ],
        ];
    }

    public function resizeServer()
    {
        return [
            'method'  => 'POST',
            'path'    => 'servers/{id}/action',
            'jsonKey' => 'resize',
            'params'  => [
                'id'       => $this->params->urlId('server'),
                'flavorId' => $this->params->flavorId(),
            ],
        ];
    }

    public function confirmServerResize()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/action',
            'params' => [
                'id'            => $this->params->urlId('server'),
                'confirmResize' => $this->params->nullAction(),
            ],
        ];
    }

    public function revertServerResize()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/action',
            'params' => [
                'id'           => $this->params->urlId('server'),
                'revertResize' => $this->params->nullAction(),
            ],
        ];
    }

    public function createServerImage()
    {
        return [
            'method'  => 'POST',
            'path'    => 'servers/{id}/action',
            'jsonKey' => 'createImage',
            'params'  => [
                'id'       => $this->params->urlId('server'),
                'metadata' => $this->notRequired($this->params->metadata()),
                'name'     => $this->isRequired($this->params->name('server')),
            ],
        ];
    }

    public function getAddresses()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/ips',
            'params' => ['id' => $this->params->urlId('server')],
        ];
    }

    public function getAddressesByNetwork()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/ips/{networkLabel}',
            'params' => [
                'id'           => $this->params->urlId('server'),
                'networkLabel' => $this->params->networkLabel(),
            ],
        ];
    }

    public function getServerMetadata()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata',
            'params' => ['id' => $this->params->urlId('server')]
        ];
    }

    public function putServerMetadata()
    {
        return [
            'method' => 'PUT',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id'       => $this->params->urlId('server'),
                'metadata' => $this->params->metadata()
            ]
        ];
    }

    public function postServerMetadata()
    {
        return [
            'method' => 'POST',
            'path'   => 'servers/{id}/metadata',
            'params' => [
                'id'       => $this->params->urlId('server'),
                'metadata' => $this->params->metadata()
            ]
        ];
    }

    public function getServerMetadataKey()
    {
        return [
            'method' => 'GET',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => $this->params->urlId('server'),
                'key' => $this->params->key(),
            ]
        ];
    }

    public function deleteServerMetadataKey()
    {
        return [
            'method' => 'DELETE',
            'path'   => 'servers/{id}/metadata/{key}',
            'params' => [
                'id'  => $this->params->urlId('server'),
                'key' => $this->params->key(),
            ]
        ];
    }
}
