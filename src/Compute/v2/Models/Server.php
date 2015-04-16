<?php

namespace OpenStack\Compute\v2\Models;

use OpenStack\Common\Resource\IsCreatable;
use OpenStack\Common\Resource\IsDeletable;
use OpenStack\Common\Resource\IsListable;
use OpenStack\Common\Resource\IsRetrievable;
use OpenStack\Common\Resource\IsRetrievableInterface;
use OpenStack\Common\Resource\IsUpdateable;
use OpenStack\Common\Resource\AbstractResource;
use OpenStack\Compute\v2\Enum;

/**
 * @property \OpenStack\Compute\v2\Api $api
 */
class Server extends AbstractResource implements
    IsCreatable,
    IsUpdateable,
    IsDeletable,
    IsRetrievable,
    IsListable
{
    public $id;
    public $ipv4;
    public $ipv6;
    public $addresses;
    public $created;
    public $updated;
    public $flavor;
    public $hostId;
    public $image;
    public $links;
    public $metadata;
    public $name;
    public $progress;
    public $status;
    public $tenantId;
    public $userId;

    protected $resourceKey = 'server';
    protected $resourcesKey = 'servers';
    protected $markerKey = 'id';

    protected $aliases = [
        'block_device_mapping_v2' => 'blockDeviceMapping',
        'accessIPv4' => 'ipv4',
        'accessIPv6' => 'ipv6',
        'tenant_id'  => 'tenantId',
        'user_id'    => 'userId',
    ];

    public function populateFromArray(array $data)
    {
        parent::populateFromArray($data);

        $this->created = new \DateTimeImmutable($this->created);
        $this->updated = new \DateTimeImmutable($this->updated);

        if (isset($data['flavor'])) {
            $this->flavor = $this->model('Flavor', $data['flavor']);
        }

        if (isset($data['image'])) {
            $this->image = $this->model('Image', $data['image']);
        }
    }

    /**
     * @param array $userOptions
     * @return self
     */
    public function create(array $userOptions)
    {
        $response = $this->execute($this->api->postServer(), $userOptions);

        return $this->populateFromResponse($response);
    }

    /**
     * @return self
     */
    public function update()
    {
        $response = $this->execute($this->api->putServer(), $this->getAttrs(['id', 'name', 'ipv4', 'ipv6']));

        return $this->populateFromResponse($response);
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->execute($this->api->deleteServer(), $this->getAttrs(['id']));
    }

    /**
     * @return self
     */
    public function retrieve()
    {
        $response = $this->execute($this->api->getServer(), $this->getAttrs(['id']));

        return $this->populateFromResponse($response);
    }

    /**
     * @param string $newPassword The new root password for the server
     */
    public function changePassword($newPassword)
    {
        $this->execute($this->api->changeServerPassword(), [
            'id' => $this->id,
            'password' => $newPassword
        ]);
    }

    /**
     * @param string $type The type of reboot that will be performed. Either SOFT or HARD is supported.
     */
    public function reboot($type = Enum::REBOOT_SOFT)
    {
        if (!in_array($type, ['SOFT', 'HARD'])) {
            throw new \RuntimeException('Reboot type must either be SOFT or HARD');
        }

        $this->execute($this->api->rebootServer(), [
            'id'   => $this->id,
            'type' => $type,
        ]);
    }

    /**
     * @param array $options
     */
    public function rebuild(array $options)
    {
        $options['id'] = $this->id;
        $response = $this->execute($this->api->rebuildServer(), $options);
        
        $this->populateFromResponse($response);
    }

    /**
     * @param string $flavorId The UUID of the new flavor your server will be based on.
     */
    public function resize($flavorId)
    {
        $response = $this->execute($this->api->resizeServer(), [
            'id' => $this->id,
            'flavorId' => $flavorId,
        ]);

        $this->populateFromResponse($response);
    }

    public function confirmResize()
    {
        $this->execute($this->api->confirmServerResize(), ['confirmResize' => null, 'id' => $this->id]);
    }

    public function revertResize()
    {
        $this->execute($this->api->revertServerResize(), ['revertResize' => null, 'id' => $this->id]);
    }

    public function createImage(array $options)
    {
        $options['id'] = $this->id;
        $this->execute($this->api->createServerImage(), $options);
    }

    public function listAddresses(array $options = [])
    {
        $options['id'] = $this->id;

        $data = (isset($options['networkLabel'])) ? $this->api->getAddressesByNetwork() : $this->api->getAddresses();
        $response = $this->execute($data, $options);
        return $response->json()['addresses'];
    }

    public function getMetadata()
    {
        $response = $this->execute($this->api->getServerMetadata(), ['id' => $this->id]);
        return $response->json()['metadata'];
    }

    public function resetMetadata(array $metadata)
    {
        $response = $this->execute($this->api->putServerMetadata(), ['id' => $this->id, 'metadata' => $metadata]);
        return $response->json()['metadata'];
    }

    public function mergeMetadata(array $metadata)
    {
        $response = $this->execute($this->api->postServerMetadata(), ['id' => $this->id, 'metadata' => $metadata]);
        return $response->json()['metadata'];
    }

    public function getMetadataItem($key)
    {
        $response = $this->execute($this->api->getServerMetadataKey(), ['id' => $this->id, 'key' => $key]);
        return $response->json()['metadata'][$key];
    }

    public function deleteMetadataItem($key)
    {
        $this->execute($this->api->deleteServerMetadataKey(), ['id' => $this->id, 'key' => $key]);
    }
}