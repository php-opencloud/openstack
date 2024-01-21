<?php

namespace OpenStack\Sample\Compute\v2;

use OpenStack\Networking\v2\Models\InterfaceAttachment;

class InterfaceAttachmentTest extends TestCase
{
    public function testCreate()
    {
        $server = $this->createServer();
        $network = $this->getNetworkService()->createNetwork(['name' => $this->randomStr()]);
        $this->getNetworkService()->createSubnet(
            [
                'name'      => $this->randomStr(),
                'networkId' => $network->id,
                'ipVersion' => 4,
                'cidr'      => '10.20.40.0/24',
            ]
        );


        $replacements = [
            '{serverId}' => $server->id,
            '{networkId}' => $network->id,
        ];

        /** @var \OpenStack\Networking\v2\Models\InterfaceAttachment $interfaceAttachment */
        require_once $this->sampleFile('servers/create_interface_attachment.php', $replacements);

        $this->assertInstanceOf(InterfaceAttachment::class, $interfaceAttachment);
        $this->assertEquals($network->id, $interfaceAttachment->netId);

        $port = $this->getNetworkService()->getPort($interfaceAttachment->portId);
        $port->retrieve();

        $server->detachInterface($interfaceAttachment->portId);
        $port->waitUntilDeleted();

        $this->deleteNetwork($network);
        $this->deleteServer($server);
    }
}