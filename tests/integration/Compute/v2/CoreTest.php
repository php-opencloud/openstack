<?php

namespace OpenStack\Integration\Compute\v2;

use OpenStack\BlockStorage\v2\Models\Volume;
use OpenStack\Compute\v2\Models\Flavor;
use OpenStack\Compute\v2\Models\HypervisorStatistic;
use OpenStack\Compute\v2\Models\Hypervisor;
use OpenStack\Compute\v2\Models\Image;
use OpenStack\Compute\v2\Models\Keypair;
use OpenStack\Compute\v2\Models\Limit;
use OpenStack\Compute\v2\Models\Server;
use OpenStack\Integration\TestCase;
use OpenStack\Integration\Utils;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Models\SecurityGroup;
use OpenStack\Networking\v2\Models\Network;
use OpenStack\Networking\v2\Models\Subnet;
use OpenStack\Networking\v2\Service as NetworkService;
use OpenStack\BlockStorage\v2\Service as BlockStorageService;
use OpenStack\Networking\v2\Extensions\SecurityGroups\Service as SecurityGroupService;
use RuntimeException;

class CoreTest extends TestCase
{
    // Test environment constants
    const NETWORK = 'phptest_network';
    const SUBNET = 'phptest_subnet';
    const VOLUME = 'phptest_volume';

    const SECGROUP = 'phptest_secgroup';

    const IMAGE = 'cirros';

    /** @var NetworkService */
    private $networkService;

    /** @var BlockStorageService */
    private $blockStorageService;

    /** @var SecurityGroupService */
    private $secgroupService;

    /** @var  Network */
    private $network;

    /** @var  Subnet */
    private $subnet;

    /** @var  Volume */
    private $volume;

    /** @var SecurityGroup */
    private $secgroup;

    // Core test
    private $service;
    private $serverId;
    private $adminPass;
    private $imageId;
    private $flavorId;
    private $keypairName;
    private $volumeAttachmentId;

    private function getService() : \OpenStack\Compute\v2\Service
    {
        if (null === $this->service) {
            $this->service = Utils::getOpenStack()->computeV2();
        }

        return $this->service;
    }

    private function getNetworkService() : \OpenStack\Networking\v2\Service
    {
        if (!$this->networkService) {
            $this->networkService = Utils::getOpenStack()->networkingV2();
        }

        return $this->networkService;
    }

    private function getSecurityGroupService(): SecurityGroupService
    {
        $this->secgroupService = $this->secgroupService ?? Utils::getOpenStack()->networkingV2ExtSecGroups();
        return $this->secgroupService;
    }

    private function getBlockStorageService()
    {
        if (!$this->blockStorageService) {
            $this->blockStorageService = Utils::getOpenStack()->blockStorageV3();
        }

        return $this->blockStorageService;
    }

    private function searchImages($name)
    {
        foreach ($this->getService()->listImages() as $image) {
            if (strpos($image->name, $name) !== false) {
                $this->imageId = $image->id;
                break;
            }
        }

        if (!$this->imageId) {
            throw new RuntimeException(sprintf('Unable to find image "%s". Make sure this image is available for integration test.', $name));
        }
    }

    protected function setUp(): void
    {
        $this->network = $this->getNetworkService()->createNetwork(
            [
                'name'         => self::NETWORK,
                'adminStateUp' => true,
            ]
        );

        $this->logStep('Created network {name} with {id}', ['name' => $this->network->name, 'id' => $this->network->id]);

        $this->subnet = $this->getNetworkService()->createSubnet(
            [
                'name'      => self::SUBNET,
                'networkId' => $this->network->id,
                'ipVersion' => 4,
                'cidr'      => '10.20.30.0/24',
            ]
        );

        $this->logStep('Created subnet {name} with {id}', ['name' => $this->subnet->name, 'id' => $this->subnet->id]);

        $this->volume = $this->getBlockStorageService()->createVolume(
            [
                'name' => self::VOLUME,
                'description' => '',
                'size' => 1
            ]
        );

        $this->logStep('Created volume {name} with {id}', ['name' => $this->volume->name, 'id' => $this->volume->id]);

        $this->secgroup = $this->getSecurityGroupService()->createSecurityGroup(['name' => self::SECGROUP]);

        $this->logStep('Created security group {secgroup}', ['secgroup' => self::SECGROUP]);
    }

    public function runTests()
    {
        $this->startTimer();

        // Manually trigger setUp
        $this->setUp();

        $this->searchImages(self::IMAGE);

        // Servers
        $this->createServer();

        try {
            $this->updateServer();
            $this->retrieveServer();
            $this->serverMetadata();

            // Server actions
            //$this->changeServerPassword();
            $this->stopServer();
            $this->startServer();
            $this->resizeServer();
            $this->confirmServerResize();
            $this->rebuildServer();
            $this->rescueServer();
            $this->createServerImage();
            $this->rebootServer();

            // Security groups
            $this->addSecurityGroupToServer();
            $this->listServerSecurityGroups();
            $this->removeServerSecurityGroup();

            // Volume attachments
            $this->attachVolumeToServer();
            $this->listVolumeAttachmentsForServer();
            $this->detachVolumeFromServer();

            // Flavors
            $this->createFlavor();
            $this->listFlavors();
            $this->getFlavor();

            // Images
            $this->listImages();
            $this->getImage();
            $this->imageMetadata();
            $this->deleteServerImage();

            // Keypairs
            $this->listKeypairs();
            $this->createKeypair();
            $this->getKeypair();
            $this->deleteKeypair();

            // Limits
            $this->getLimits();

            // Hypervisors
            $this->listHypervisors();
            $this->getHypervisorsStatistics();
            $this->getHypervisor();

            // Console
            $this->getVncConsole();

            // Interface attachments
            $this->createInterfaceAttachment();
        } finally {
            $this->logger->info('Tearing down');
            // Teardown
            $this->deleteServer();
            $this->deleteFlavor();
            $this->subnet->delete();
            $this->network->delete();
            $this->volume->delete();
            $this->secgroup->delete();
        }

        $this->outputTimeTaken();
    }

    private function createServer()
    {
        $flavorId = getenv('OS_FLAVOR');

        if (!$flavorId) {
            throw new RuntimeException('OS_FLAVOR env var must be set');
        }

        $replacements = [
            '{serverName}' => $this->randomStr(),
            '{imageId}'    => $this->imageId,
            '{flavorId}'   => $flavorId,
            '{networkId}'  => $this->network->id
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/create_server.php', $replacements);

        $server->waitUntilActive();

        self::assertInstanceOf('OpenStack\Compute\v2\Models\Server', $server);
        self::assertNotEmpty($server->id);
        self::assertNotEmpty($server->adminPass);

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
        require_once $this->sampleFile('servers/update_server.php', $replacements);

        self::assertInstanceOf('OpenStack\Compute\v2\Models\Server', $server);
        self::assertEquals($name, $server->name);

        $server->waitUntilActive(false);

        $this->logStep('Updated server ID to use this name: NAME', ['ID' => $this->serverId, 'NAME' => $name]);
    }

    private function deleteServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/delete_server.php', $replacements);

        // Needed so that subnet and network can be removed
        $server->waitUntilDeleted();
        $this->logStep('Deleted server ID', ['ID' => $this->serverId]);
    }

    private function retrieveServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/get_server.php', $replacements);

        self::assertInstanceOf('OpenStack\Compute\v2\Models\Server', $server);
        self::assertEquals($this->serverId, $server->id);
        self::assertNotNull($server->created);
        self::assertNotNull($server->updated);
        self::assertNotNull($server->name);
        self::assertNotNull($server->ipv4);
        self::assertNotNull($server->status);
        self::assertInstanceOf(Image::class, $server->image);
        self::assertInstanceOf(Flavor::class, $server->flavor);

        $this->logStep('Retrieved the details of server ID', ['ID' => $this->serverId]);
    }

    private function serverMetadata()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/reset_server_metadata.php', $replacements);
        $this->logStep('Reset metadata of server {serverId}', $replacements);

        require_once $this->sampleFile('servers/get_server_metadata.php', $replacements);
        $this->logStep('Retrieved metadata of server {serverId}', $replacements);

        require_once $this->sampleFile('servers/delete_server_metadata_item.php', $replacements);
        $this->logStep('Deleted metadata key of server {serverId}', $replacements);
    }

    private function changeServerPassword()
    {
        $this->adminPass = $this->randomStr();

        $replacements = [
            '{serverId}'    => $this->serverId,
            '{newPassword}' => $this->adminPass,
        ];

        require_once $this->sampleFile('servers/change_server_password.php', $replacements);

        $this->logStep('Changed root password of server {serverId} to {newPassword}', $replacements);
    }

    private function resizeServer()
    {
        $resizeFlavorId = getenv('OS_RESIZE_FLAVOR');
        if (!$resizeFlavorId) {
            throw new RuntimeException('OS_RESIZE_FLAVOR env var must be set');
        }

        $replacements = [
            '{serverId}' => $this->serverId,
            '{flavorId}' => $resizeFlavorId,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/resize_server.php', $replacements);

        $server->waitUntil('VERIFY_RESIZE');

        $this->logStep('Resized server {serverId} to flavor {flavorId}', $replacements);
    }

    private function confirmServerResize()
    {
        $this->logger->info('Waiting for status VERIFY_RESIZE');
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/confirm_server_resize.php', $replacements);

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
        require_once $this->sampleFile('servers/rebuild_server.php', $replacements);

        $server->waitUntilActive();

        $this->logStep('Rebuilt server {serverId}', $replacements);
    }

    private function rescueServer()
    {
        $replacements = [
            '{serverId}'  => $this->serverId,
            '{imageId}'   => $this->imageId,
            '{adminPass}' => $this->adminPass,
        ];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/rescue_server.php', $replacements);

        $server->waitUntil('RESCUE');

        require_once $this->sampleFile('servers/unrescue_server.php', $replacements);

        $server->waitUntilActive();

        $this->logStep('Rescued server {serverId}', $replacements);
    }

    private function rebootServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/reboot_server.php', $replacements);

        $server->waitUntilActive();

        $this->logStep('Rebooted server {serverId}', $replacements);
    }

    private function stopServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/stop_server.php', $replacements);

        $server->waitUntil('SHUTOFF', false);

        $this->logStep('Stopped server {serverId}', $replacements);
    }

    private function startServer()
    {
        $replacements = ['{serverId}' => $this->serverId];

        /** @var $server \OpenStack\Compute\v2\Models\Server */
        require_once $this->sampleFile('servers/start_server.php', $replacements);

        $server->waitUntilActive();

        $this->logStep('Started server {serverId}', $replacements);
    }

    private function createFlavor()
    {
        $replacements = [
            '{flavorName}' => $this->randomStr()
        ];

        /** @var $flavor \OpenStack\Compute\v2\Models\Flavor */
        require_once $this->sampleFile('flavors/create_flavor.php', $replacements);

        self::assertInstanceOf('\OpenStack\Compute\v2\Models\Flavor', $flavor);

        $this->flavorId = $flavor->id;
        $this->logStep('Created flavor {id}', ['{id}' => $flavor->id]);
    }

    private function deleteFlavor()
    {
        $replacements = ['{flavorId}' => $this->flavorId];

        require_once $this->sampleFile('flavors/delete_flavor.php', $replacements);

        $this->logStep('Deleted flavor ID', ['ID' => $this->flavorId]);
    }

    private function listFlavors()
    {
        require_once $this->sampleFile('flavors/list_flavors.php', []);

        $this->logStep('Listed all available flavors');
    }

    private function getFlavor()
    {
        $replacements = ['{flavorId}' => 1];

        require_once $this->sampleFile('flavors/get_flavor.php', $replacements);

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
        require_once $this->sampleFile('images/create_server_image.php', $replacements);

        $server->waitWithCallback(function (Server $server) {
            return !$server->taskState;
        }, false);

        $this->searchImages($name);

        $this->logStep('Created an image for server {serverId} with name {imageName}', $replacements);
    }

    private function listImages()
    {
        require_once $this->sampleFile('images/list_images.php', []);

        $this->logStep('Listed all available images');
    }

    private function getImage()
    {
        $replacements = ['{imageId}' => $this->imageId];

        require_once $this->sampleFile('images/get_image.php', $replacements);

        $this->logStep('Retrieved details for image {imageId}', $replacements);
    }

    private function imageMetadata()
    {
        $replacements = ['{imageId}' => $this->imageId];

        /** @var $image \OpenStack\Compute\v2\Models\Image */
        require_once $this->sampleFile('images/reset_image_metadata.php', $replacements);
        $this->logStep('Reset metadata of image {imageId}', $replacements);

        require_once $this->sampleFile('images/retrieve_image_metadata.php', $replacements);
        $this->logStep('Retrieved metadata of image {imageId}', $replacements);

        require_once $this->sampleFile('images/delete_image_metadata_item.php', $replacements + ['{metadataKey}']);
        $this->logStep('Deleted metadata key of image {imageId}', $replacements);
    }

    private function deleteServerImage()
    {
        $replacements = ['{imageId}' => $this->imageId];
        require_once $this->sampleFile('images/delete_image.php', $replacements);
        $this->logStep('Deleted image {imageId}', $replacements);
    }

    private function listKeypairs()
    {
        /** @var $keypairs \Generator */
        require_once $this->sampleFile('keypairs/list_keypairs.php', []);

        self::assertInstanceOf(\Generator::class, $keypairs);

        $this->logStep('Listed all keypairs');
    }

    private function createKeypair()
    {
        $replacements = [
            '{name}'      => $this->randomStr(),
            '{publicKey}' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCp4H/vDGnLi0QgWgMsQkv//FEz0xgv/mujVX+XCh6fHXxc/PbaASY+MsoI2Xr238cG9eaeAAUvbpJuEuHQ0M9WX97bvsWaWzLQ9F6hzLAwUBGxcG8cSh1nB3Ah7alR2nbIZ1N94yE72hXLb1AGogJ97NBVIph438BCXUNejqoOBsXL8UBP3RGdPnTHJ/6XSMaNTQAJruQMoQwecyGFQmuS2IEy2mBOmSldD6JZirHpj7PTCKJY4CS89QChGpKIeOymKn4tEQQVVtNFUyULEMdin88H1yMftPfq7QqH+ULFT2X2XvP3CI+sESq84lrIcVu7LjJCRIwlKsnMu2ESYCdz foo@bar.com'
        ];

        require_once $this->sampleFile('keypairs/create_keypair.php', $replacements);
        /**@var Keypair $keypair */

        self::assertInstanceOf(Keypair::class, $keypair);
        self::assertEquals($replacements['{name}'], $keypair->name);
        self::assertEquals($replacements['{publicKey}'], $keypair->publicKey);

        $this->keypairName = $keypair->name;
        $this->logStep('Created keypair name {name}', ['{name}' => $keypair->name]);
    }

    private function getKeypair()
    {
        $replacements = [
            '{name}' => $this->keypairName,
        ];

        require_once $this->sampleFile('keypairs/get_keypair.php', $replacements);

        /**@var Keypair $keypair */
        self::assertInstanceOf(Keypair::class, $keypair);

        self::assertEquals($replacements['{name}'], $keypair->name);

        $this->logStep('Retrieved details for keypair {name}', $replacements);
    }

    private function deleteKeypair()
    {
        $replacements = [
            '{name}' => $this->keypairName,
        ];

        require_once $this->sampleFile('keypairs/delete_keypair.php', $replacements);
        $this->logStep('Deleted keypair name {name}', ['{name}' => $this->keypairName]);
    }

    private function listHypervisors()
    {
        require_once $this->sampleFile('hypervisors/list_hypervisors.php', []);

        $this->logStep('Listed all available hypervisors');
    }

    private function getHypervisor()
    {
        $replacements = [
            '{hypervisorId}' => '1',
        ];

        require_once $this->sampleFile('hypervisors/get_hypervisor.php', $replacements);

        /**@var Hypervisor $hypervisor */
        self::assertInstanceOf(Hypervisor::class, $hypervisor);
        self::assertEquals($replacements['{hypervisorId}'], $hypervisor->id);

        $this->logStep('Retrieved details for hypervisor id {hypervisorId}', $replacements);
    }

    private function getHypervisorsStatistics()
    {
        require_once $this->sampleFile('hypervisors/get_hypervisors_statistics.php', []);

        /**@var HypervisorStatistic $hypervisorStatistics */
        self::assertInstanceOf(HypervisorStatistic::class, $hypervisorStatistics);

        $this->logStep('Retrieved hypervisors statistics');
    }

    private function getLimits()
    {
        require_once $this->sampleFile('limits/get_limits.php', []);

        /**@var Limit $limit */
        self::assertInstanceOf(Limit::class, $limit);

        $this->logStep('Retrieved tenant limit');
    }

    private function addSecurityGroupToServer()
    {
        $replacements = [
            '{serverId}'     => $this->serverId,
            '{secGroupName}' => self::SECGROUP,
        ];

        require_once $this->sampleFile('servers/add_security_group.php', $replacements);

        /**@var Server $server*/
        $this->logStep('Added security group {secGroupName} to server {serverId}', $replacements);
    }

    private function listServerSecurityGroups()
    {
        $replacements = [
            '{serverId}' => $this->serverId
        ];

        require_once $this->sampleFile('servers/list_security_groups.php', $replacements);

        /**@var \Generator $securityGroups */
        self::assertInstanceOf(\Generator::class, $securityGroups);

        $this->logStep('Listed all security groups attached to server {serverId}', $replacements);
    }

    private function removeServerSecurityGroup()
    {
        $replacements = [
            '{serverId}'     => $this->serverId,
            '{secGroupName}' => self::SECGROUP,
        ];

        require_once $this->sampleFile('servers/remove_security_group.php', $replacements);

        $this->logStep(/** @lang text */ 'Delete security group {secGroupName} from server {serverId}', $replacements);
    }

    private function attachVolumeToServer()
    {
        $replacements = [
            '{serverId}' => $this->serverId,
            '{volumeId}' => $this->volume->id
        ];

        require_once $this->sampleFile('servers/attach_volume_attachment.php', $replacements);
        /** @var \OpenStack\BlockStorage\v2\Models\VolumeAttachment $volumeAttachment */
        $this->volumeAttachmentId = $volumeAttachment->id;

        $this->volume->waitUntil('in-use');

        $this->logStep(
            'Attached volume {volumeId} to server {serverId} with volume attachment id {volumeAttachmentId}',
            array_merge($replacements, ['{volumeAttachmentId}' => $volumeAttachment->id])
        );
    }

    private function listVolumeAttachmentsForServer()
    {
        $replacements = [
            '{serverId}' => $this->serverId
        ];

        require_once $this->sampleFile('servers/list_volume_attachments.php', $replacements);

        $this->logStep('Retrieved volume attachments for server {serverId}', $replacements);
    }

    private function detachVolumeFromServer()
    {
        $replacements = [
            '{serverId}'           => $this->serverId,
            '{volumeAttachmentId}' => $this->volumeAttachmentId,
        ];

        require_once $this->sampleFile('servers/detach_volume_attachment.php', $replacements);

        $this->volume->waitUntil('available');

        $this->logStep('Detached volume attachments for server {serverId}', $replacements);
    }

    private function getVncConsole()
    {
        $replacements = [
            '{serverId}' => $this->serverId
        ];

        require_once $this->sampleFile('servers/get_server_vnc_console.php', $replacements);

        $this->logStep('Get VNC console for server {serverId}', $replacements);
    }

    private function createInterfaceAttachment()
    {
        $replacements = [
            '{serverId}' => $this->serverId,
            '{networkId}' => $this->network->id
        ];

        require_once $this->sampleFile('servers/create_interface_attachment.php', $replacements);

        $this->logStep('Create interface attachment for server {serverId}', $replacements);
    }

    private function getConsoleOutput()
    {
        $replacements = [
            '{serverId}' => $this->serverId
        ];

        require_once $this->sampleFile('servers/get_server_console_output.php', $replacements);

        $this->logStep('Get console output for server {serverId}', $replacements);
    }
}
