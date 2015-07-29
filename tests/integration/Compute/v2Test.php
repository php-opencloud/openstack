<?php

namespace OpenStack\Integration\Compute;

use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Integration\TestCase;
use OpenStack\OpenStack;

class V2Test extends TestCase
{
    private $service;
    private $serverId;
    private $adminPass;
    private $imageId;

    private function getService()
    {
        if (null === $this->service) {
            $this->service = (new OpenStack())->computeV2($this->getAuthOpts());
        }

        return $this->service;
    }

    protected function getBasePath()
    {
        return __DIR__ . '/../../../samples/compute/v2/';
    }

    private function searchImages($name)
    {
        foreach ($this->getService()->listImages() as $image) {
            if (strpos($image->name, $name) !== false) {
                $this->imageId = $image->id;
                return;
            }
        }

        $this->logger->emergency('No image found');
    }

    public function runTests()
    {
        $this->searchImages('cirros');
        $this->startTimer();

        // Servers
        $this->createServer();

        try {
            $this->updateServer();
            $this->retrieveServer();
            $this->serverMetadata();

            // Server actions
            //$this->changeServerPassword();
            $this->resizeServer();
            $this->confirmServerResize();
            $this->rebuildServer();
            $this->createServerImage();
            $this->rebootServer();

            // Flavors
            $this->listFlavors();
            $this->getFlavor();

            // Images
            $this->listImages();
            $this->getImage();
            $this->imageMetadata();
            $this->deleteServerImage();
        } finally {
            // Teardown
            $this->deleteServer();
        }

        $this->outputTimeTaken();
    }

    private function createServer()
    {
        $replacements = [
            '{serverName}' => $this->randomStr(),
            '{imageId}'    => $this->imageId,
            '{flavorId}'   => 1,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        $path = $this->sampleFile($replacements, 'create_server.php');
        require_once $path;

        $server->waitUntilActive(false);

        $this->assertInstanceOf('OpenStack\Compute\v2\Models\Server', $server);
        $this->assertNotEmpty($server->id);
        $this->assertNotEmpty($server->adminPass);

        $this->serverId = $server->id;
        $this->adminPass = $server->adminPass;

        $this->logStep('Created server {id}', ['{id}' => $server->id]);
    }

    private function updateServer()
    {
        $name = $this->randomStr();

        $replacements = [
            '{serverId}' => $this->serverId,
            '{newName}'  => $name,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        $path = $this->sampleFile($replacements, 'update_server.php');
        require_once $path;

        $this->assertInstanceOf('OpenStack\Compute\v2\Models\Server', $server);
        $this->assertEquals($name, $server->name);

        $server->waitUntilActive(false);

        $this->logStep('Updated server ID to use this name: NAME', ['ID' => $this->serverId, 'NAME' => $name]);
    }

    private function deleteServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        $path = $this->sampleFile($replacements, 'delete_server.php');
        require_once $path;

        $this->logStep('Deleted server ID', ['ID' => $this->serverId]);
    }

    private function retrieveServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        $path = $this->sampleFile($replacements, 'get_server.php');
        require_once $path;

        $this->assertInstanceOf('OpenStack\Compute\v2\Models\Server', $server);
        $this->assertEquals($this->serverId, $server->id);
        $this->assertNotNull($server->created);
        $this->assertNotNull($server->updated);
        $this->assertNotNull($server->name);
        $this->assertNotNull($server->ipv4);
        $this->assertNotNull($server->status);
        $this->assertInstanceOf(Image::class, $server->image);
        $this->assertInstanceOf(Flavor::class, $server->flavor);

        $this->logStep('Retrieved the details of server ID', ['ID' => $this->serverId]);
    }

    private function serverMetadata()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile($replacements, 'reset_server_metadata.php');
        $this->logStep('Reset metadata of server {serverId}', $replacements);

        require_once $this->sampleFile($replacements, 'get_server_metadata.php');
        $this->logStep('Retrieved metadata of server {serverId}', $replacements);

        require_once $this->sampleFile($replacements, 'delete_server_metadata_item.php');
        $this->logStep('Deleted metadata key of server {serverId}', $replacements);
    }

    private function changeServerPassword()
    {
        $this->adminPass = $this->randomStr();

        $replacements = [
            '{serverId}'    => $this->serverId,
            '{newPassword}' => $this->adminPass,
        ];

        require_once $this->sampleFile($replacements, 'change_server_password.php');

        $this->logStep('Changed root password of server {serverId} to {newPassword}', $replacements);
    }

    private function resizeServer()
    {
        $resizeFlavorId = getenv('OS_RESIZE_FLAVOR');
        if (!$resizeFlavorId) {
            throw new \RuntimeException('OS_RESIZE_FLAVOR env var must be set');
        }

        $replacements = [
            '{serverId}' => $this->serverId,
            '{flavorId}' => $resizeFlavorId,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile($replacements, 'resize_server.php');

        $server->waitUntil('VERIFY_RESIZE');

        $this->logStep('Resized server {serverId} to flavor {flavorId}', $replacements);
    }

    private function confirmServerResize()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile($replacements, 'confirm_server_resize.php');

        $server->waitUntilActive();

        $this->logStep('Confirmed resize of server {serverId}', $replacements);
    }

    private function rebuildServer()
    {
        $replacements = [
            '{serverId}'  => $this->serverId,
            '{imageId}'   => $this->imageId,
            '{adminPass}' => $this->adminPass,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile($replacements, 'rebuild_server.php');

        $server->waitUntilActive();

        $this->logStep('Rebuilt server {serverId}', $replacements);
    }

    private function rebootServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile($replacements, 'reboot_server.php');

        $server->waitUntilActive(false);

        $this->logStep('Rebooted server {serverId}', $replacements);
    }

    private function listFlavors()
    {
        require_once $this->sampleFile([], 'list_flavors.php');

        $this->logStep('Listed all available flavors');
    }

    private function getFlavor()
    {
        $replacements = ['{flavorId}' => 1];

        require_once $this->sampleFile($replacements, 'get_flavor.php');

        $this->logStep('Retrieved details for flavor {flavorId}', $replacements);
    }

    private function createServerImage()
    {
        $name = $this->randomStr();

        $replacements = [
            '{serverId}'  => $this->serverId,
            '{imageName}' => $name,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile($replacements, 'create_server_image.php');

        $server->waitWithCallback(function (Server $server) {
            return !$server->taskState;
        }, false);

        $this->searchImages($name);

        $this->logStep('Created an image for server {serverId} with name {imageName}', $replacements);
    }

    private function listImages()
    {
        require_once $this->sampleFile([], 'list_images.php');

        $this->logStep('Listed all available images');
    }

    private function getImage()
    {
        $replacements = ['{imageId}' => $this->imageId];

        require_once $this->sampleFile($replacements, 'get_image.php');

        $this->logStep('Retrieved details for image {imageId}', $replacements);
    }

    private function imageMetadata()
    {
        $replacements = ['{imageId}' => $this->imageId];

        /** @var $image \OpenStack\Compute\v2\Models\Image */
        require_once $this->sampleFile($replacements, 'reset_image_metadata.php');
        $this->logStep('Reset metadata of image {imageId}', $replacements);

        require_once $this->sampleFile($replacements, 'retrieve_image_metadata.php');
        $this->logStep('Retrieved metadata of image {imageId}', $replacements);

        require_once $this->sampleFile($replacements + ['{metadataKey}'], 'delete_image_metadata_item.php');
        $this->logStep('Deleted metadata key of image {imageId}', $replacements);
    }

    private function deleteServerImage()
    {
        $replacements = ['{imageId}' => $this->imageId];
        require_once $this->sampleFile($replacements, 'delete_image.php');
        $this->logStep('Deleted image {imageId}', $replacements);
    }
}