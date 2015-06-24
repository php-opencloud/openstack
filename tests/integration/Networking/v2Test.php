<?php

namespace OpenStack\Integration\Networking;

use OpenStack\Networking\v2\Models\Network;
use OpenStack\Integration\TestCase;
use OpenStack\OpenStack;

class V2Test extends TestCase
{
    private $service;
    private $networkId;

    private function getService()
    {
        if (null === $this->service) {
            $this->service = (new OpenStack())->networkingV2(['region' => getenv('OS_REGION')]);
        }

        return $this->service;
    }

    protected function getBasePath()
    {
        return __DIR__ . '/../../../samples/networking/v2/';
    }

    public function runTests()
    {
        $this->startTimer();

        $this->createNetwork();

        try {
            $this->updateNetwork();
            $this->retrieveNetwork();
        } finally {
            // Teardown
            $this->deleteNetwork();
        }

        $this->outputTimeTaken();
    }

    private function createNetwork()
    {
        $replacements = [
            '{networkName}' => 'fakeNetwork'
        ];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'create_network.php');
        require_once $path;

        $this->assertInstanceOf('OpenStack\Networking\v2\Models\Network', $network);
        $this->assertNotEmpty($network->id);

        $this->networkId = $network->id;

        $this->logStep('Created network {id}', ['{id}' => $this->networkId]);
    }

    private function updateNetwork()
    {
        $name = $this->randomStr();

        $replacements = [
            '{networkId}' => $this->networkId,
            '{newName}'  => $name,
        ];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'update_network.php');
        require_once $path;

        $this->assertInstanceOf('OpenStack\Networking\v2\Models\Network', $network);
        $this->assertEquals($name, $network->name);

        $this->logStep('Updated network ID to use this name: NAME', ['ID' => $this->networkId, 'NAME' => $name]);
    }

    private function retrieveNetwork()
    {
        $replacements = ['{networkId}' => $this->networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'get_network.php');
        require_once $path;

        $this->assertInstanceOf('OpenStack\Networking\v2\Models\Network', $network);
        $this->assertEquals($this->networkId, $network->id);

        $this->logStep('Retrieved the details of network ID', ['ID' => $this->networkId]);
    }

    private function deleteNetwork()
    {
        $replacements = ['{networkId}' => $this->networkId];

        /** @var $network \OpenStack\Networking\v2\Models\Network */
        $path = $this->sampleFile($replacements, 'delete_network.php');
        require_once $path;

        $this->logStep('Deleted network ID', ['ID' => $this->networkId]);
    }
}
